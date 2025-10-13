<?php

namespace App\Listeners;

use App\Events\WhatsappTextProcess;
use App\Models\Messages;
use App\Services\Whatsapp\Utils\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Illuminate\Support\Str;
class TextWhatsappProcess
{
    use Message;
    private WhatsAppCloudApi $whatsapp;
    /**
     * Create the event listener.
     */
    public function __construct(

    )
    {
        $this->whatsapp = new WhatsAppCloudApi([
            'from_phone_number_id' => env('WHATSAPP_FROM_PHONE_NUMBER_ID'),
            'access_token' => env('WHATSAPP_TOKEN'),
        ]);
    }

    /**
     * Handle the event.
     */
    public function handle(WhatsappTextProcess $event): void
    {
        // dd($event->data);
        if ( $event->data['request']->context()){
            $message = Messages::where('wam_id', $event->data['request']->context()->replyingToMessageId())->first();
        }
        switch($event->data['type']){
            case 'button':
                $data = [
                    'type' => 'contact',
                    'contact' => $event->data['contact'],
                    'message' => $this->getData(
                        $event->data['request'],
                        $event->data['type'],
                        $event->data['request']->text(),
                        '',
                        $message->id,
                    ),
                ];
                break;
            case 'text':
                $data = [
                    'type' => 'contact',
                    'contact' => $event->data['contact'],
                    'message' => $this->getData(
                        $event->data['request'],
                        $event->data['type'],
                        '',
                        $event->data['request']->message(),
                        ( isset($message) ? $message->id : null ),
                    ),
                ];
                break;
            case 'location':
                $data = [
                    'type' => 'contact',
                    'contact' => $event->data['contact'],
                    'message' => $this->getData(
                            $event->data['request'],
                            $event->data['type'],
                            collect([
                                "latitude" => $event->data['request']->latitude(),
                                "longitude" => $event->data['request']->longitude(),
                                "address" => $event->data['request']->address(),
                                "name" => $event->data['request']->name(),
                            ])->toJson(),
                            '',
                            ( isset($message) ? $message->id : null ),
                            ''
                        ),
                ];
                break;
            case 'media':
                $data = [
                    'type' => 'contact',
                    'contact' => $event->data['contact'],
                    'message' => $this->getData(
                            $event->data['request'],
                            $event->data['type'],
                            '',
                            $this->compileMedia($event->data['request']->imageId(), $event->data['request']->mimeType()),
                            ( isset($message) ? $message->id : null ),
                            $event->data['request']->caption()
                        ),
                ];
                break;
            case 'contact':
                $data = [
                    'type' => 'contact',
                    'contact' => $event->data['contact'],
                    'message' => $this->getData(
                        $event->data['request'],
                        $event->data['type'],
                        collect([
                            "name" => $event->data['request']->formattedName(),
                            "phones" => $event->data['request']->phones(),
                            "company" => $event->data['request']->companyName(),
                            "emails" => $event->data['request']->emails(),
                            "birthday" => $event->data['request']->birthday(),
                        ])->toJson(),
                        '',
                        ( isset($message) ? $message->id : null ),
                    ),
                ];
                break;

            case 'reaction':
                $message = Messages::where('wam_id', $event->data['request']->messageID())->with(['conversation','contact','user'])->first();
                $data = [
                    'type' => 'contact',
                    'contact' => $event->data['contact'],
                    'message' => $this->getData($event->data, $event->data['request']->emoji(), '', $message->id),
                ];
                break;
            case 'interactive':
                $data = [
                    'type' => 'contact',
                    'contact' => $event->data['contact'],
                    'message' => $this->getData(
                        $event->data['request'],
                        $event->data['type'],
                        $event->data['request']->itemId(),
                        $event->data['request']->title(),
                        ( isset($message) ? $message->id : null ),
                    ),
                ];
                break;
            case 'system':
            case 'unknown':
            case 'order':
                $data = [];
                break;
        }
        $this->saveMessage($data);

    }
    public function compileMedia(string $mediaId, string $mimeType): string
    {
        //  dd($this->whatsapp);
        $download_resource = $this->whatsapp->downloadMedia($mediaId);
        $mimeType = explode(';', $mimeType)[0];
        $type = explode('/',$mimeType);
        $fileName = Str::uuid()->toString().'.'.$type[1];
        Storage::disk('public')->put($fileName, $download_resource->body());
        return $fileName;
    }
}
