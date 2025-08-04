<?php

namespace App\Listeners;

use App\Events\WhatsappCloseConversation;
use App\Events\WhatsappDelivered;
use App\Services\Whatsapp\Utils\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

class CloseWhatsappConversation
{
    use Message;
    private WhatsAppCloudApi $whatsapp;
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
    public function handle(WhatsappCloseConversation $event): void
    {
        // dd($event->data['conversation']);
        if ( $event->data['pesquisa'] ){
            $response = $this->whatsapp->sendTemplate($event->data['conversation']->contact->contact->phone_id,'avalie','pt_BR');
            $decoded = $response->decodedBody();
            $message = [
                "name" => $event->data['conversation']->contact->contact->name,
                "wa_id" => $event->data['conversation']->contact->contact->phone_id,
                "wam_id" => $decoded['messages'][0]['id'],
                "type" => 'rate',
                "body" => 'Avalie nosso atendimento',
                "caption" => null,
                "data" => null,
                "status" => 'delivered',
                'conversation_id' => $event->data['conversation']->id,
            ];
            $data = [
                'type' => 'user',
                'message' => $message,
                'contact' => $event->data['conversation']->contact->contact,
                'user' => $event->data['conversation']->user,
                'conversation' => $event->data['conversation'],
            ];
            $message_ = $this->saveMessage($data);
        }else{
            $m =  'A PowInternet agradece seu contato.';
            $response = $this->whatsapp->sendTextMessage($event->data['conversation']->contact->contact->phone_id,$m,true);
            $decoded = $response->decodedBody();
            $message = [
                "name" => $event->data['conversation']->contact->contact->name,
                "wa_id" => $event->data['conversation']->contact->contact->phone_id,
                "wam_id" => $decoded['messages'][0]['id'],
                "type" => 'finish',
                "body" => $m,
                "caption" => null,
                "data" => null,
                "status" => 'delivered',
                'conversation_id' => $event->data['conversation']->id,
            ];
            $data = [
                'type' => 'user',
                'message' => $message,
                'contact' => $event->data['conversation']->contact->contact,
                'user' => $event->data['conversation']->user,
                'conversation' => $event->data['conversation'],
            ];
            $message_ = $this->saveMessage($data);
            $this->closeConversation($event->data['conversation']->id);
        }
        WhatsappDelivered::dispatch($message_);
    }
}
