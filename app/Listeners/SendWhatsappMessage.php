<?php

namespace App\Listeners;

use App\Events\WhatsappDelivered;
use App\Events\WhatsappSendMessage;
use App\Services\Whatsapp\Utils\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

class SendWhatsappMessage
{
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
    use Message;
    /**
     * Handle the event.
     */
    public function handle(WhatsappSendMessage $event): void
    {
        $response = $this->whatsapp->sendTextMessage($event->message->wa_id, $event->message->body, true);
        $decoded = $response->decodedBody();
        $event->message->wam_id = $decoded['messages'][0]['id'];
        $event->message->status = 'delivered';
        $event->message->save();
        WhatsappDelivered::dispatch($event->message);
    }
}
