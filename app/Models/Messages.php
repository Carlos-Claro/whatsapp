<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

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
        'conversation_id',
        'related_id',
        'created_at',
        'updated_at',

    ];
    protected $appends = ['image_address'];
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d/m/Y H:i:s',
        ];
    }
    public function related(): HasOne
    {
        return $this->hasOne(Messages::class, 'id', 'related_id');
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
    public function imageAddress(): Attribute
    {
        return Attribute::get(function(){
            if ($this->type == 'image'){
                return [Storage::disk('public')->mimeType($this->body), Storage::disk('public')->url($this->body)];
            }
            return '';
        });
    }
}
