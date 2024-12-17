<?php
namespace App\Services\Whatsapp\Utils;

use App\Http\Resources\ConversationResumeCollection;
use App\Models\Contacts;
use App\Models\Conversations;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait Message{
    public function send(Request $request, $message, Contacts $contact): void {
        $response = $this->whatsapp->sendTextMessage($message['wa_id'], $message['body'], true);
        $decoded = $response->decodedBody();
        $message['wam_id'] = $decoded['messages'][0]['id'];
        $message['status'] = 'delivered';
        $data = [
            'type' => 'contact',
            'message' => $message,
            'contact' => $contact,
            'user' => $request->user(),
        ];
        $item = $this->saveMessage($data);
        dd($item);
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
    protected $contact;
    public function activeConversation($contact){
        $conversation = Conversations::whereHas('members', function(Builder $query) use($contact){
            return $query->whereHasMorph('memberable', Contacts::class, function (Builder $query) use($contact) {
                return $query->where('memberable_id', $contact->id);
            });
        })->where('conversations.status', 0)->first();
        return $conversation;
    }
    public function saveMessage($data){
        if( ! isset($data['conversation']) || ! $data['conversation'] ){
            if ( $data['type'] == 'contact' ){
                $conversation = $this->activeConversation($data['contact']);
            }
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
        return [$conversation, $message];
    }

}
