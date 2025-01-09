<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Messages extends Model
{
    protected $fillable = [
        'wam_id',
        'name',
        'wa_id',
        'type',
        'body',
        'caption',
        'data',
        'status',
        'contact_id',
        'conversation_id',
        'created_at',
        'updated_at',

    ];
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d/m/Y H:i:s',
        ];
    }
    public function conversation(): HasOne
    {
        return $this->hasOne(Conversations::class, 'id', 'conversation_id');
    }
    public function memberable(): MorphTo
    {
        return $this->morphTo();
    }
    public function contact(): HasOne{
        return $this->hasOne(Contacts::class, 'id', 'memberable_id');
    }

    public function user(): HasOne{
        return $this->hasOne(User::class, 'id', 'memberable_id');
    }
}
