<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Conversation_has extends Model
{
    protected $fillable = [
        "conversation_id",
        "department_id",
        "memberable_type",
        "memberable_id",
        "status",
    ];

    protected function casts(): array {
        return [
            'created_at' => 'datetime:Y-m-d H:i',
        ];
    }
    public function memberable(): MorphTo{
        return $this->morphTo();
    }
    public function department(): HasOne{
        return $this->hasOne(Departments::class, 'id', 'department_id');
    }
    public function conversation(): HasOne{
        return $this->hasOne(Conversations::class, 'id', 'conversation_id');
    }
    public function contact(): HasOne{
        return $this->hasOne(Contacts::class, 'id', 'memberable_id');
    }

    public function user(): HasOne{
        return $this->hasOne(User::class, 'id', 'memberable_id');
    }

}
