<?php

namespace App\Listeners;

use App\Events\StartConversationProcessEvent;
use App\Events\WhatsappWelcomeMessage;
use App\Models\Departments;
use App\Models\Messages;
use App\Models\Start_conversation;
use App\Services\Whatsapp\Utils\Message;
use App\Services\Whatsapp\Utils\Actions;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Netflie\WhatsAppCloudApi\Message\ButtonReply\Button;
use Netflie\WhatsAppCloudApi\Message\ButtonReply\ButtonAction;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

class StartConversationProcessListener
{
    use Message;
    use Actions;
    private WhatsAppCloudApi $whatsapp;
    private $message;
    private $contact;
    private $conversation;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->whatsapp = new WhatsAppCloudApi([
            'from_phone_number_id' => env('WHATSAPP_FROM_PHONE_NUMBER_ID'),
            'access_token' => env('WHATSAPP_TOKEN'),
        ]);
    }

    /**
     * Handle the event.
     */
    public function handle(StartConversationProcessEvent $event): void
    {
        $this->message = $event->item['message'];
        $this->validateContact($event);
        $this->validateConversation($event);
        if ( $this->conversation && $this->conversation->status == 1 ){
            exit;
        }
        if ( $this->firstMessage() ){
            $nextQuestion = Start_conversation::where('tag', 'start')->first();
            if ( ! $nextQuestion ){
                Log::notice('Não foi encontrada a mensagem: ', ['type' => 'start']);
            }
        }else{
            switch($this->message->type){
                case 'interactive':
                    if ( $event->item['message']->related_id ){
                        $related = Messages::where('id', $event->item['message']->related_id)->first();
                        $data = json_decode($related->data, true);
                        $nextQuestion = $this->verifyOptionRelated($data, $this->message->data);
                    }else{
                        $related = $this->conversation->lastMessageSystem;
                        $data = json_decode($related->data, true);

                        $nextQuestion = $this->verifyOptionRelated($data, $this->message->data);
                    }
                    break;
                case 'text':
                    $related = $this->conversation->lastMessageSystem;
                    $data = json_decode($related->data, true);
                    if ( isset($data[0]['action']) ){
                        $options = $this->{$data[0]['action']}($this->message->body);
                        if ( $options ){
                            $nextQuestion = Start_conversation::where('id', $data[0]['id_success'])->first();
                        }else{
                            $nextQuestion = Start_conversation::where('id', $data[0]['id_error'])->first();
                        }
                    }
                    break;
            }
        }
        if ( isset($nextQuestion) && $nextQuestion ){
            $this->processMessage($nextQuestion, (isset($options) ? $options : []));
        }else{
            dd('sem ação');
        }

    }

    private function processMessage($message, $addons = []){
        switch ( $message->type ) {
            case 'button':
                $rows = [];
                foreach( $message->answer['resume'] as $answer){
                    $rows[] = new Button($answer['tag'], $answer['title']);
                }
                $action = new ButtonAction($rows);
                $data = [
                    'type' => 'user',
                    'message' => $this->setMessage(
                        $message->type,
                        $message->question,
                        $message->caption,
                        $message->answer['json']
                    ),
                    'contact' => $this->contact,
                    'conversation' => $this->conversation,
                    'processMessage' => true,
                ];
                // dd($data);
                $messageSave = $this->saveMessage($data);
                $response = $this->whatsapp->sendButton(
                    $this->contact->phone_id,
                    $message->question,
                    $action
                );

                break;
            case 'text':
                $data = [
                    'type' => 'user',
                    'message' => $this->setMessage(
                        $message->type,
                        $message->question,
                        '',
                        $message->answer['json']
                    ),
                    'contact' => $this->contact,
                    'conversation' => $this->conversation,
                    'processMessage' => true,
                ];
                $messageSave = $this->saveMessage($data);
                $response = $this->whatsapp->sendTextMessage(
                    $this->contact->phone_id,
                    $message->question,
                    false
                );
                break;
            case 'department':

                break;
            case 'departments':
                $departments = Departments::select('id','title','tag')->get();
                $rows = [];
                foreach( $departments as $department){
                    $rows[] = new Button($department['tag'], $department['title']);
                }
                $action = new ButtonAction($rows);
                $question = (isset($addons) ? $addons['message'] : '') . PHP_EOL . $message->question;
                $data = [
                    'type' => 'user',
                    'message' => $this->setMessage(
                        $message->type,
                        $question,
                        $message->caption,
                        json_encode($departments, true)
                    ),
                    'contact' => $this->contact,
                    'conversation' => $this->conversation,
                    'processMessage' => true,
                ];
                $messageSave = $this->saveMessage($data);
                $response = $this->whatsapp->sendButton(
                    $this->contact->phone_id,
                    $question,
                    $action
                );
                break;
            default:
                // Handle other message types if necessary
                break;
        }
        if ( ! isset($response) ){
            return;
        }
        $decoded = $response->decodedBody();
        $messageSave->wam_id = $decoded['messages'][0]['id'];
        $messageSave->status = 'delivered';
        $messageSave->save();
    }
    private function setMessage ( $type, $text, $caption, $data ){
        return [
            "name" => $this->contact->name,
            "wa_id" => $this->contact->phone_id,
            "wam_id" => 0,
            "type" => $type,
            "body" => $text,
            "caption" => $caption,
            "data" => $data,
            "status" => 'sent',
            'conversation_id' => $this->conversation->id,
        ];
    }
    private function firstMessage(){
        if ( isset($this->conversation->lastMessageSystem) && $this->conversation->lastMessageSystem->type == 'text' ){
            return false;
        }
        if ( ! isset($this->conversation->lastMessageSystem) ){
            return true;
        }
        if ( ! isset($this->message->related_id) ){
            return true;
        }

        return false;
    }
    private function validateConversation($event){
        if ( isset($this->message->conversation) ){
            $this->conversation = $this->message->conversation;
            return true;
        }
        if ( isset($event->item['conversation'])){
            $this->conversation = $event->item['conversation'];
            return true;
        }
        if ( $event->item['message']->context()){
            $message = Messages::where('wam_id', $event->item['message']->context()->replyingToMessageId())->with('conversation')->first();
            $this->conversation = $message->conversation;
            return true;
        }
        $this->conversation = false;
        return false;

    }
    private function validateContact($event){
        if ( isset($this->message->contact) ){
            $this->contact = $this->message->contact;
            return true;
        }
        if ( isset($event->item['conversation']->contact->contact)){
            $this->contact = $event->item['conversation']->contact->contact;
            return true;
        }
        if ( isset($event->item['contact']) ){
            $this->contact = $event->item['contact'];
            return true;
        }
        $this->contact = false;
        return false;
    }
    private function verifyOptionRelated($relatedOptions, $data){
        foreach ($relatedOptions as $option){
            if ( $option['tag'] == $data ){
                return Start_conversation::where('id', $option['related']['id'])->first();
            }
        }
    }
}
