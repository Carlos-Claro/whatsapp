<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversations extends Model
{
    protected $fillable = [
        "contact_id",
        "department_id",
        "status_id",
        "hate",
        "hate_annotation",
    ];

    protected function casts(): array {
        return [
            'created_at' => 'datetime:Y-m-d H:i',
        ]
    }

    public function contact(): HasOne {
        return $this->hasOne(Contacts::class, 'id', 'contact_id');
    }
    public function messages(): HasMany{
        return $this->hasMany(Messages::class, 'conversation_id', 'id');
    }
}
