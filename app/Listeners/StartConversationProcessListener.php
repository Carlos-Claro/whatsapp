<?php

namespace App\Listeners;

use App\Events\StartConversationProcessEvent;
use App\Events\WhatsappWelcomeMessage;
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
            //primeiro contato, start auto response
            $newMessage = Start_conversation::where('tag', 'start')->first();
            if ( isset($newMessage) ){
                $this->processMessage($newMessage);
            }else{
                Log::notice('Não foi encontrada a mensagem: ', ['type' => 'start']);
            }
        }else{
            switch($this->message->type){
                case 'interactive':
                    $related = Messages::where('id', $event->item['message']->related_id)->first();
                    $data = json_decode($related->data, true);
                    $nextQuestion = $this->verifyOptionRelated($data, $this->message->data);
                    break;
                case 'text':
                    $related = $this->conversation->lastMessageSystem;
                    $data = json_decode($related->data, true);
                    if ( isset($data[0]['action']) ){
                        $options = $this->{$data[0]['action']}($this->message->body);
                        if ( $options ){
                            dd($data[0]['id_success'],$options);
                        }else{
                            dd($data[0]['id_error']);
                        }
                    }
                    break;
            }
            // dd($nextQuestion);
            if ( isset($nextQuestion) && $nextQuestion ){
                $this->processMessage($nextQuestion);
            }else{
                dd('sem ação');
            }
        }

    }

    private function processMessage($message){
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
                $decoded = $response->decodedBody();
                $messageSave->wam_id = $decoded['messages'][0]['id'];
                $messageSave->status = 'delivered';
                $messageSave->save();
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
                $decoded = $response->decodedBody();
                $messageSave->wam_id = $decoded['messages'][0]['id'];
                $messageSave->status = 'delivered';
                $messageSave->save();
                break;
            case 'department':

                break;
            default:
                // Handle other message types if necessary
                break;
        }
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
