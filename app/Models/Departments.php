<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Departments extends Model
{
    protected $fillable = [
        "title",
        "tag",
    ];

    protected function casts(): array {
        return [
            'created_at' => 'datetime:Y-m-d H:i',
        ];
    }
    public function conversations(): HasManyThrough
    {
        return $this->hasManyThrough(
                                    Conversation_has::class,
                                    Conversations::class,
                                    'department_id',
                                    'conversation_id',
                                    'id',
                                    'id'
                                );
    }
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

}
