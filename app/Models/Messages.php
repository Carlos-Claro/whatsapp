<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    public function conversation(): HasOne
    {
        return $this->hasOne(Conversations::class, 'id', 'conversation_id');
    }

}
