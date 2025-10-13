<?php

namespace App\Http\Controllers;

use App\Events\StartConversationProcessEvent;
use App\Events\WhatsappButtonProcess;
use App\Events\WhatsappDelivered;
use App\Events\WhatsappTextProcess;
use App\Models\Contacts;
use App\Models\Messages;
use App\Services\Whatsapp\Utils\Contact as UtilsContact;
use App\Services\Whatsapp\Utils\Media as UtilsMedia;
use App\Services\Whatsapp\Utils\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Netflie\WhatsAppCloudApi\Message\ButtonReply\Button;
use Netflie\WhatsAppCloudApi\WebHook;
use Netflie\WhatsAppCloudApi\WebHook\Notification;
use Netflie\WhatsAppCloudApi\WebHook\Notification\Button as NotificationButton;
use Netflie\WhatsAppCloudApi\WebHook\Notification\Contact;
use Netflie\WhatsAppCloudApi\WebHook\Notification\Interactive;
use Netflie\WhatsAppCloudApi\WebHook\Notification\Location;
use Netflie\WhatsAppCloudApi\WebHook\Notification\Media;
use Netflie\WhatsAppCloudApi\WebHook\Notification\Order;
use Netflie\WhatsAppCloudApi\WebHook\Notification\Reaction;
use Netflie\WhatsAppCloudApi\WebHook\Notification\StatusNotification;
use Netflie\WhatsAppCloudApi\WebHook\Notification\System;
use Netflie\WhatsAppCloudApi\WebHook\Notification\Text;
use Netflie\WhatsAppCloudApi\WebHook\Notification\Unknown;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

class WhatsappWebhook extends Controller
{
    use UtilsMedia, UtilsContact, Message;
    protected Notification $event;
    private array $payload;
    private WhatsAppCloudApi $whatsapp;
    public function __construct(
        private readonly WebHook $webhook,
    ){
        $this->whatsapp = new WhatsAppCloudApi([
            'from_phone_number_id' => env('WHATSAPP_FROM_PHONE_NUMBER_ID'),
            'access_token' => env('WHATSAPP_TOKEN'),
        ]);
    }
    private function setEventPayload(Request $request): void
    {
        if ( ! empty(file_get_contents('php://input')) ){
            $this->payload =  json_decode(file_get_contents('php://input'), true);
        }else{
            $this->payload = $request->all();
        }
        $this->event = $this->webhook->read($this->payload);
    }
    private function setLog(): void {
        Storage::disk('public')->append('events.log', '-------------------payload-----');
        Storage::disk('public')->append('events.log', print_r($this->payload, true));
        Storage::disk('public')->append('events.log', '-------------------event-----');
        Storage::disk('public')->append('events.log', print_r($this->event, true));
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $this->setEventPayload($request);
        $this->setLog();
        $messageExists = $this->existsMessageInDatabase($this->event->id());
        // dd($this->event, $messageExists);
        if ( $messageExists->isEmpty() ){
            $contact = $this->existsContact([
                'name' => $this->event->customer()->name(),
                'phone' => $this->event->customer()->phoneNumber(),
                'phone_id' => (int) $this->event->customer()->phoneNumber(),
            ]);

            if ($this->event instanceof Contact) {
                $data = [
                    'type' => 'contact',
                    'request' => $this->event,
                    'contact' => $contact,
                ];
                WhatsappTextProcess::dispatch($data);
            }

            //Text Type Notification
            if ($this->event instanceof Text) {
                $data = [
                    'type' => 'text',
                    'request' => $this->event,
                    'contact' => $contact,
                ];
                WhatsappTextProcess::dispatch($data);
            }

            //Media Type Notification
            if ($this->event instanceof Media) {
                $data = [
                    'type' => 'media',
                    'request' => $this->event,
                    'contact' => $contact,
                ];
                WhatsappTextProcess::dispatch($data);
            }

            //Location Type Notification
            if ($this->event instanceof Location) {
                $data = [
                    'type' => 'location',
                    'request' => $this->event,
                    'contact' => $contact,
                ];
                WhatsappTextProcess::dispatch($data);
            }

            //Reaction Type Notification
            if ($this->event instanceof Reaction) {
                $data = [
                    'type' => 'reaction',
                    'request' => $this->event,
                    'contact' => $contact,
                ];
                WhatsappTextProcess::dispatch($data);
                // Log::info('Reaction: ', [$this->event]);
            }

            //Button Type Notification
            if ($this->event instanceof Button) {

                WhatsappButtonProcess::dispatch($this->event);
                // Log::info('Button: ', [$this->event]);
            }

            if ($this->event instanceof NotificationButton) {
                WhatsappButtonProcess::dispatch($this->event);
                // Log::info('ButtonN: ', [$this->event]);
            }

            //System Type Notification

            if ($this->event instanceof System) {
                Log::info('System: ', [$this->event]);
            }

            //Order Type Notification
            if ($this->event instanceof Order) {
                Log::info('Order: ', [$this->event]);
            }

            //Interactive Type Notification
            if ($this->event instanceof Interactive) {
                $data = [
                    'type' => 'interactive',
                    'request' => $this->event,
                    'contact' => $contact,
                ];
                WhatsappTextProcess::dispatch($data);
                // StartConversationProcessEvent::dispatch([
                //     'type' => 'interactive',
                //     'message' => $this->event,
                //     'contact' => $contact,
                // ]);
            }
            //Unknown Type Notification
            if ($this->event instanceof Unknown) {
                Log::notice('Unknown: ', [$this->event]);
            }

        }
        //StatusNotification Type Notification
        if ($this->event instanceof StatusNotification) {
            $status = $this->event->status(); // sent, delivered, read
            $wam = Messages::query()->where('wam_id', $this->event->id())->first();
            if (! empty($wam)) {
                $wam->status = $status;
                $wam->save();
            }
            WhatsappDelivered::dispatch($wam);
        }
    }

}
