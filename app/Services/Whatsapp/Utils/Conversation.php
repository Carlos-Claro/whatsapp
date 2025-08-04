<?php
namespace App\Services\Whatsapp\Utils;
use App\Models\Contacts;
use App\Models\Conversations;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

trait Conversation {
        public function getResume(User $user): Collection {
        $activeConversations = $this->conversationsUser($user, 0);
        $allConversations = $this->conversationsAllUser($user, 0);
        $closedConversations = $this->conversationsNoUser(1);
        $nobodyConversations = $this->conversationsNobody(2);
        return collect([
            'open' => [
                'count' => $activeConversations->count(),
                'items' => $this->toArray($activeConversations),
            ],
            'all' => [
                'count' => $allConversations->count(),
                'items' => $this->toArray($allConversations),
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
    public function conversationsAllUser(User $user, $status = 0)
    {
        $conversation =  Conversations::whereHas('members', function(Builder $query) use($user){
            return $query->whereHasMorph('memberable', User::class, function (Builder $query) use($user) {
                return $query->where('memberable_id', '<>', $user->id);
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
    public function getMessages(int $id) {
        return Messages::where('conversation_id', $id)->with(['related'])->get();
    }
    public function closeConversation(int $id){
        $conversation = Conversations::where('id', $id)->first();
        if ( isset($conversation) ){
            $conversation->status = 1;
            $conversation->save();
        }
        return $conversation;
    }
}
