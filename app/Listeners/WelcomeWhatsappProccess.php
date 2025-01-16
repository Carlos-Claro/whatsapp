<?php

namespace App\Listeners;

use App\Events\WhatsappDelivered;
use App\Events\WhatsappWelcomeMessage;
use App\Services\Whatsapp\Utils\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

class WelcomeWhatsappProccess
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
    public function handle(WhatsappWelcomeMessage $event): void
    {
        // dd($event);
        $response = $this->whatsapp->sendTemplate($event->item['data']['contact']->phone_id,'saudacao','pt_BR');
        $decoded = $response->decodedBody();
        $message = [
            "name" => $event->item['data']['contact']->name,
            "wa_id" => $event->item['data']['contact']->phone_id,
            "wam_id" => $decoded['messages'][0]['id'],
            "type" => 'welcome',
            "body" => 'Olá, este é o canal de comunicação da POWInternet, selecione abaixo o canal que deseja atendimento:',
            "caption" => null,
            "data" => null,
            "status" => 'delivered',
            'conversation_id' => $event->item['conversation']->id,
        ];
        $data = [
            'type' => 'system',
            'message' => $message,
            'conversation' => $event->item['conversation'],
        ];
        // 'contact' => $event->item['data']['contact'],
        $message_ = $this->saveMessage($data);
        WhatsappDelivered::dispatch($message_);
    }
}
