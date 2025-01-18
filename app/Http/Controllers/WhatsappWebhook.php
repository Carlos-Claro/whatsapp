<?php

namespace App\Http\Controllers;

use App\Events\WhatsappButtonProcess;
use App\Events\WhatsappDelivered;
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
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $this->payload = json_decode(file_get_contents('php://input'), true);
        $this->event = $this->webhook->read($this->payload);
        Storage::disk('public')->append('events.log', '-------------------payload-----');
        Storage::disk('public')->append('events.log', print_r($this->payload, true));
        Storage::disk('public')->append('events.log', '-------------------event-----');
        Storage::disk('public')->append('events.log', print_r($this->event, true));
        $messageExists = $this->existsMessageInDatabase($this->event->id());
        if ( $messageExists->isEmpty() ){
            $contact = $this->existsContact([
                'name' => $this->event->customer()->name(),
                'phone' => $this->event->customer()->phoneNumber(),
                'phone_id' => (int) $this->event->customer()->phoneNumber(),
            ]);
            if ($this->event instanceof Contact) {
                Log::info('Contacto: ', [$this->event]);
            }

            //Text Type Notification
            if ($this->event instanceof Text) {
                    $message = $this->saveMessage([
                        'message' => [
                            "wam_id" => $this->event->id(),
                            "name" => $this->event->customer()->name(),
                            "wa_id" => $this->event->customer()->phoneNumber(),
                            "type" => 'text',
                            "created_at" => $this->event->receivedAt(),
                            "body" => $this->event->message(),
                            "caption" => null,
                            "data" => null,
                            "status" => 'delivered',

                        ],
                        'contact' => $contact,
                        'type' => 'contact',
                    ]);
            }

            //Media Type Notification
            if ($this->event instanceof Media) {
                    $message = $this->saveMessage([
                        'message' => [
                            "wam_id" => $this->event->id(),
                            "name" => $this->event->customer()->name(),
                            "wa_id" => $this->event->customer()->phoneNumber(),
                            "type" => 'image',
                            "timestamp" => ($this->event->receivedAt())->format('Y-m-d H:i:s'),
                            "body" => $this->compileMedia($this->event->imageId(), $this->event->mimeType()),
                            "caption" => $this->event->caption(),
                            "data" => null,
                            "status" => 'delivered',

                        ],
                        'contact' => $contact,
                        'type' => 'contact',
                   ]);
            }

            //Location Type Notification
            if ($this->event instanceof Location) {
                Log::info('Location: ', [$this->event]);
            }

            //Reaction Type Notification
            if ($this->event instanceof Reaction) {
                Log::info('Reaction: ', [$this->event]);
            }

            //Button Type Notification
            if ($this->event instanceof Button) {
                WhatsappButtonProcess::dispatch($this->event);
                Log::info('Button: ', [$this->event]);
            }

            if ($this->event instanceof NotificationButton) {
                WhatsappButtonProcess::dispatch($this->event);
                Log::info('ButtonN: ', [$this->event]);
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
                Log::info('Interactive: ', [$this->event]);
            }
            //Unknown Type Notification
            if ($this->event instanceof Unknown) {
                Log::info('Unknown: ', [$this->event]);
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
        private static function compileMedia(string $mediaId, string $mimeType): string
    {
        $download_resource = $this->whatsapp->downloadMedia($mediaId);
        $mimeType = explode(';', $mimeType)[0];
        $fileName = Str::uuid()->toString().'.'.$mimeType;
        Storage::disk('public')->put($fileName, $download_resource->body());
        return $fileName;
    }
}
