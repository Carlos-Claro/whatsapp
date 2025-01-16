<?php

namespace App\Listeners;

use App\Events\WhatsappButtonProcess;
use App\Events\WhatsappDelivered;
use App\Models\Conversations;
use App\Models\Messages;
use App\Models\User;
use App\Services\Whatsapp\Utils\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class ButtonWhatsappProcess
{
    use Message;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WhatsappButtonProcess $event): void
    {
        Storage::disk('public')->append('events.log', '---- button ----');
        Storage::disk('public')->append('events.log', print_r($event, true));
        $message = Messages::where('wam_id', $event->button->context()->replyingToMessageId())->with(['conversation','contact','user'])->first();
        switch($message->type){
            case 'rate':
                $this->rate($message, $event->button);
                break;
            case 'welcome':
                $this->welcome($message, $event->button);
                break;
        }
    }
    private function welcome($message, $button){

        $message_ = [
            "name" => $message->conversation->contact->contact->name,
            "wa_id" => $message->conversation->contact->contact->phone_id,
            "wam_id" => $button->id(),
            "type" => 'related',
            "body" => $button->text(),
            "caption" => null,
            "data" => null,
            "status" => 'read',
            "related_id" => $message->id,
            'conversation_id' => $message->conversation->id,
        ];
        $data = [
            'type' => 'contact',
            'message' => $message_,
            'contact' => $message->conversation->contact->contact,
            'conversation' => $message->conversation,
        ];
        if ( $button->text() == 'Suporte técnico' ){
            $data['user'] = User::find(1);
        } elseif ( $button->text() == 'Financeiro' ){
            $data['user'] = User::find(2);
        }else{
            $data['user'] = User::find(3);
        }
        $data['associate_conversation'] = true;
        // dd($data);
        $message_save = $this->saveMessage($data);
        WhatsappDelivered::dispatch($message_save);
    }
    private function rate($message, $button){
        $message_ = [
            "name" => $message->conversation->contact->contact->name,
            "wa_id" => $message->conversation->contact->contact->phone_id,
            "wam_id" => $button->id(),
            "type" => 'related',
            "body" => $button->text(),
            "caption" => null,
            "data" => null,
            "status" => 'read',
            "related_id" => $message->id,
            'conversation_id' => $message->conversation->id,
        ];
        $data = [
            'type' => 'contact',
            'message' => $message_,
            'contact' => $message->conversation->contact->contact,
            'user' => $message->conversation->user->user,
            'conversation' => $message->conversation,
        ];
        // dd($message->conversation->id);
        $message_save = $this->saveMessage($data);
        $conversation = Conversations::find($message->conversation->id);
        // dd($conversation);
        if ( isset($conversation) ){
            $conversation->status = 1;
            $conversation->rate_annotation = $button->text();
            $conversation->save();
        }
    }
}
