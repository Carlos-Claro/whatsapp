<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'created_at',
        'updated_at',

    ];
}
