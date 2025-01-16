<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Conversations extends Model
{
    protected $fillable = [
        "contact_id",
        "department_id",
        "status",
        "rate_annotation",
    ];

    protected function casts(): array {
        return [
            'created_at' => 'datetime:Y-m-d H:i',
        ];
    }

    public function members(): HasMany{
        return $this->hasMany(Conversation_has::class, 'conversation_id', 'id');
    }
    public function contact(): HasOne {
        return $this->hasOne(Conversation_has::class, 'conversation_id', 'id')->whereHasMorph('memberable', Contacts::class)->with('contact');
    }
    public function user(): HasOne {
        return $this->hasOne(Conversation_has::class, 'conversation_id', 'id')->whereHasMorph('memberable', User::class);
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
}
