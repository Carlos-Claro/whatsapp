<?php
namespace App\Services\Whatsapp\Utils;


use App\Events\WhatsappNewMessage;
use App\Events\WhatsappReceived;
use App\Events\WhatsappWelcomeMessage;
use App\Models\Contacts;
use App\Models\Conversations;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Illuminate\Support\Str;


trait Message{
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
                $conversation = Conversations::create(['status' => 0]);
                $data['contact']->conversations()->create(['conversation_id' => $conversation->id,'status' => 1]);
                if ( isset($data['user']) ){
                    $data['user']->conversations()->create(['conversation_id' => $conversation->id, 'status' => 1]);
                }
                $welcome = true;
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
            $message->memberable()->associate($data['user']);
            $message->save();
        } else {
            $message->memberable()->associate($data['contact']);
            $message->save();
        }
        if ( $welcome ){
            $item = ['message' => $message, 'conversation' => $conversation, 'data' => $data];
            WhatsappWelcomeMessage::dispatch($item);
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
    public function getResume(User $user): Collection {
        $activeConversations = $this->conversationsUser($user, 0);
        $closedConversations = $this->conversationsNoUser(1);
        $nobodyConversations = $this->conversationsNobody(0);
        return collect([
            'open' => [
                'count' => $activeConversations->count(),
                'items' => $this->toArray($activeConversations),
            ],
            'closed' => [
                'count' => $closedConversations->count(),
                'items' => $this->toArray($closedConversations),
            ],
            'nobody' => [
                'count' => $nobodyConversations->count(),
                'items' => $this->toArray($nobodyConversations),
            ],
        ]);
    }
    private function toArray($conversations): Collection {
        $return = [];
        foreach( $conversations as $conversation ){
            $return[] = $this->arrayConversation($conversation);
        }
        return collect($return);
    }
    private function arrayConversation($conversation): array {
        return [
            'id' => $conversation->id,
            'status' => $conversation->status,
            'lastMessage' => $conversation->lastMessage,
            'contact' => $conversation->contact->contact,
            'unReadMessages' => $conversation->unReadMessages->count(),
        ];
    }
    public function conversationsUser(User $user, $status = 0)
    {
        $conversation =  Conversations::whereHas('members', function(Builder $query) use($user){
            return $query->whereHasMorph('memberable', User::class, function (Builder $query) use($user) {
                return $query->where('memberable_id', $user->id);
            });
        })->where('conversations.status', $status);
        $results = $conversation->with(['members', 'contact', 'unReadMessages'])->get();
        return $results;
    }
    public function conversationsNoUser($status = 0)
    {
        $conversation =  Conversations::where('conversations.status', $status);
        $results = $conversation->with(['members', 'contact', 'unReadMessages'])->get();
        return $results;
    }
    public function conversationsNobody($status = 0)
    {
        $conversation =  Conversations::whereDoesntHave('members', function(Builder $query) {
            return $query->whereHasMorph('memberable', User::class);
        })->where('conversations.status', $status);
        $results = $conversation->with(['members', 'contact', 'unReadMessages'])->get();

        return $results;
    }
    protected $contact;
    public function activeConversation($contact){
        $conversation = Conversations::whereHas('members', function(Builder $query) use($contact){
            return $query->whereHasMorph('memberable', Contacts::class, function (Builder $query) use($contact) {
                return $query->where('memberable_id', $contact->id);
            });
        })->where('conversations.status', 0)->first();
        return $conversation;
    }

    public function getMessages(int $id) {
        return Messages::where('conversation_id', $id)->with(['related'])->get();
    }
    public function markMessageAsRead(int $id){
        $message = Messages::where('id', $id)->first();
        if ( isset($message) ){
            $this->whatsapp->markMessageAsRead($message->wam_id);
            $message->status = 'read';
            $message->save();
        }
        return $message;
    }
    public function closeConversation(int $id){
        $conversation = Conversations::where('id', $id)->first();
        if ( isset($conversation) ){
            $conversation->status = 1;
            $conversation->save();
        }
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
}
