<?php
namespace App\Services\Whatsapp\Utils;

use App\Models\Contacts;
use App\Models\Conversations;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;


trait Message{

    public function __construct(){

    }

    public function saveMessage($data){
        if( ! isset($data['conversation']) ){
            $conversation = $this->activeConversation($data['contact']);
            if ( ! isset($conversation) || ! $conversation ){
                $conversation = Conversations::create(['status' => 0]);
                $data['contact']->conversations()->create(['conversation_id' => $conversation->id,'status' => 1]);
                if ( isset($data['user']) ){
                    $data['user']->conversations()->create(['conversation_id' => $conversation->id, 'status' => 1]);
                }
            }
        }
        else{
            $conversation = $data['conversation'];
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
        $closedConversations = $this->conversationsUser($user, 1);
        $nobodyConversations = $this->conversationsNobody(0);
        return collect([
            'all' => [
                'count' => $activeConversations->count() + $closedConversations->count(),
            ],
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
            $return[] = [
                'id' => $conversation->id,
                'hate' => $conversation->hate,
                'lastMessage' => $conversation->lastMessage,
                'contact' => $conversation->contact->contact,
            ];
        }
        return collect($return);
    }
    public function conversationsUser(User $user, $status = 0)
    {
        $conversation =  Conversations::whereHas('members', function(Builder $query) use($user){
            return $query->whereHasMorph('memberable', User::class, function (Builder $query) use($user) {
                return $query->where('memberable_id', $user->id);
            });
        })->where('conversations.status', $status);
        $results = $conversation->with(['members', 'contact'])->get();
        return $results;
    }
    public function conversationsNobody($status = 0)
    {
        $conversation =  Conversations::whereDoesntHave('members', function(Builder $query) {
            return $query->whereHasMorph('memberable', User::class);
        })->where('conversations.status', $status);
        $results = $conversation->with(['members', 'contact'])->get();

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
        return Messages::where('conversation_id', $id)->get();
    }
}
