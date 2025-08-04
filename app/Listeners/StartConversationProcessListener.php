<?php

namespace App\Listeners;

use App\Events\StartConversationProcessEvent;
use App\Events\WhatsappWelcomeMessage;
use App\Models\Start_conversation;
use App\Services\Whatsapp\Utils\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Netflie\WhatsAppCloudApi\Message\ButtonReply\Button;
use Netflie\WhatsAppCloudApi\Message\ButtonReply\ButtonAction;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

class StartConversationProcessListener
{
    use Message;
    private WhatsAppCloudApi $whatsapp;
    private $message;
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
        if ( ! $event->item['message']->related_id ){
            //primeiro contato, start auto response
            $newMessage = Start_conversation::where('tag', 'start')->first();
            if ( isset($newMessage) ){
                $response = $this->processMessage($newMessage);
            }else{
                Log::notice('Não foi encontrada a mensagem: ', ['type' => 'start']);
            }
        }else{
            // busca pergunta anterior
        }

    }
    private function processMessage($message){
        switch ( $message->type ) {
            case 'button':
                //[{"tag": "sim", "title": "Sim", "related": {"id": 3}}, {"tag": "nao", "title": "Não"}]
                $rows = [];
                foreach( $message->answer['resume'] as $answer){
                    $rows[] = new Button($answer['tag'], $answer['title']);
                }
                $action = new ButtonAction($rows);
                $data = [
                    'type' => 'user',
                    'message' => $this->setMessage(
                        $message->type,
                        $this->message->contact,
                        $message->question,
                        $message->caption,
                        $message->answer['json'],
                        $this->message->conversation
                    ),
                    'contact' => $this->message->contact,
                    'conversation' => $this->message->conversation,
                    'processMessage' => true,
                ];
                // dd($data);
                $messageSave = $this->saveMessage($data);

                $response = $this->whatsapp->sendButton(
                    $this->message->contact->phone_id,
                    $message->question,
                    $action
                );
                $decoded = $response->decodedBody();
                $messageSave->wam_id = $decoded['messages'][0]['id'];
                $messageSave->status = 'delivered';
                $messageSave->save();
                break;
            case 'text':

                break;
            case 'department':

                break;
            default:
                // Handle other message types if necessary
                break;
        }
    }
    private function setMessage ( $type, $contact, $text, $caption, $data, $conversation ){
        return [
            "name" => $contact->name,
            "wa_id" => $contact->phone_id,
            "wam_id" => 0,
            "type" => $type,
            "body" => $text,
            "caption" => $caption,
            "data" => $data,
            "status" => 'sent',
            'conversation_id' => $conversation->id,
        ];
    }
}
