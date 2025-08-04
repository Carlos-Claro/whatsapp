<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Netflie\WhatsAppCloudApi\WebHook\Notification\Contact;

class Conversations extends Model
{
    /**
     * Statuses
     * 0 = Open
     * 1 = Closed
     * 2 = Started
     */

    protected $fillable = [
        "id_empresa",
        "contact_id",
        "department_id",
        "status",
        "rate_annotation",
    ];

    protected function casts(): array {
        return [
            'created_at' => 'datetime:d/m/Y H:i',
        ];
    }

    public function members(): HasMany{
        return $this->hasMany(Conversation_has::class, 'conversation_id', 'id');
    }
    public function contact(): HasOne {
        return $this->hasOne(Conversation_has::class, 'conversation_id', 'id')->whereHasMorph('memberable', Contacts::class)->with('contact');
    }
    public function user(): HasOne {
        return $this->hasOne(Conversation_has::class, 'conversation_id', 'id')->whereHasMorph('memberable', User::class)->with('user');
    }
    public function messages(): HasMany{
        return $this->hasMany(Messages::class, 'conversation_id', 'id');
    }
    public function unReadMessages(): HasMany{
        return $this->hasMany(Messages::class, 'conversation_id', 'id')->whereHasMorph('memberable', Contacts::class)->where('status', 'delivered');
    }
    public function lastMessage(): HasOne {
        return $this->hasOne(Messages::class, 'conversation_id', 'id')->latestOfMany();
    }
    public function lastMessageSystem(): HasOne {
        return $this->hasOne(Messages::class, 'conversation_id', 'id')->where('memberable_type', null)->orderBy('created_at', 'desc');
    }
    public function company(): HasOne {
        return $this->hasOne(Empresas::class, 'id', 'empresa_id');
    }
}
