<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Contacts extends Model
{
    //
    protected $fillable = [
        'name',
        'email',
        'phone',
        'phone_id',
        'created_at',
        'updated_at',

    ];
    public function conversations(): MorphMany
    {
        return $this->morphMany(Conversation_has::class,'memberable');
    }
}
