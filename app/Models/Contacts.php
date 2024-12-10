<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
