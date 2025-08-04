<?php
namespace App\Services\Whatsapp\Utils;


use App\Events\StartConversationProcessEvent;
use App\Events\WhatsappNewMessage;
use App\Events\WhatsappReceived;
use App\Events\WhatsappWelcomeMessage;
use App\Models\Contacts;
use App\Models\Conversations;
use App\Models\Messages;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;


trait Message{
    use Conversation;
    private WhatsAppCloudApi $whatsapp;
    public function __construct(){
        $this->whatsapp = new WhatsAppCloudApi([
            'from_phone_number_id' => env('WHATSAPP_FROM_PHONE_NUMBER_ID'),
            'access_token' => env('WHATSAPP_TOKEN'),
        ]);
    }

    public function saveMessage($data){
        $welcome = false;
        if( ! isset($data['conversation']) ){
            $conversation = $this->activeConversation($data['contact']);
            if ( ! isset($conversation) ){
                $conversation = Conversations::create(['status' => 2]);
                $data['contact']->conversations()->create(['conversation_id' => $conversation->id,'status' => 1]);
                if ( isset($data['user']) ){
                    $data['user']->conversations()->create(['conversation_id' => $conversation->id, 'status' => 1]);
                }
            }
        }
        else{
            $conversation = $data['conversation'];
        }
        if ( isset($data['associate_conversation']) && $data['associate_conversation'] ){
            $data['user']->conversations()->create(['conversation_id' => $conversation->id, 'status' => 1]);
            $data['user']->save();
        }
        $data['message']['conversation_id'] = $conversation->id;
        $message = Messages::create($data['message']);

        if ( $data['type'] == 'user' ){
            if ( isset($data['user'])){
                $message->memberable()->associate($data['user']);
                $message->save();
            }
        } else {
            $message->memberable()->associate($data['contact']);
            $message->save();
        }
        // dd($data);
        if ( $conversation->status == 2 && ! isset($data['processMessage']) ){
            $item = ['message' => $message, 'conversation' => $conversation, 'data' => $data];
            StartConversationProcessEvent::dispatch($item);
        }
        WhatsappNewMessage::dispatch($conversation);
        WhatsappReceived::dispatch($this->arrayConversation($conversation));
        return $message;
    }
    public function existsMessageInDatabase ($id){
        return Messages::where('wam_id', $id)
                        ->orderBy('created_at', 'desc')
                        ->limit(1)
                        ->get();
    }
    public function activeConversation($contact){
        $conversation = Conversations::whereHas('members', function(Builder $query) use($contact){
            return $query->whereHasMorph('memberable', Contacts::class, function (Builder $query) use($contact) {
                return $query->where('memberable_id', $contact->id);
            });
        })->whereIn('conversations.status', [0,2])->first();
        return $conversation;
    }

    public function getData($request, $type, $data = '', $body = '', $related_id = null, $caption = null){
        return [
            "wam_id" => $request->id(),
            "name" => $request->customer()->name(),
            "wa_id" => $request->customer()->phoneNumber(),
            "type" => $type == 'media' ? 'image' : $type,
            "created_at" => $request->receivedAt(),
            "body" => $body,
            "caption" => $caption,
            "data" => $data,
            "status" => 'delivered',
            "related_id" => $related_id,
        ];
    }
    public function markMessageAsRead(int $id){
        $message = Messages::where('id', $id)->first();
        if ( isset($message) ){
            // restaurar após testes.
            // $this->whatsapp->markMessageAsRead($message->wam_id);
            $message->status = 'read';
            $message->save();
        }
        return $message;
    }
}
