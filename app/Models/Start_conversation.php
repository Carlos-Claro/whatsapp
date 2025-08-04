<?php

namespace App\Models;

use App\Casts\AnswerJson;
use Illuminate\Database\Eloquent\Model;

class Start_conversation extends Model
{
    /**
     * type = [ button, text, department
     * status = [ 0 = inactive, 1 = active ]
     * answer = {[tag: 'tag', title: 'title']}
     */

    protected $table = 'start_conversation';
    protected $fillable = [
        'id',
        'start_conversation_id',
        'tag',
        'sequence',
        'question',
        'answer',
        'type',
        'status',
        'department_id',
        'created_at',
        'updated_at',
    ];
    protected function casts(): array
    {
        return [
            'answer' => AnswerJson::class,
        ];
    }
    public function related()
    {
        return $this->hasOne(Start_conversation::class, 'id', 'start_conversation_id');
    }

}
