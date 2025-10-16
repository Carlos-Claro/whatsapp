<?php

namespace App\Casts;

use App\Models\Start_conversation;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AnswerJson implements CastsAttributes
{
    public function __construct() {
    }
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $data = json_decode($value, true);
        if ( ! isset($data['resume']) ){
            $data = [];
            $data['json'] = $value;
            $data['array'] = [];
            $data['resume'] = json_decode($value, true);

        }
        if ( isset($data['resume']) && is_array($data['resume']) ){
            foreach ($data['resume'] as $key => $item) {
                $data['array'][$key] = $item;
                if ( isset($item['tag'])){
                    $filter = [
                        'start_conversation_id' => $model->id,
                        'tag' => $item['tag'],
                    ];
                    $i = Start_conversation::where($filter)->first();
                    if ( $i ){
                        $data['array'][$key]['related'] = $i;
                    }
                }elseif( isset($item['action'])){
                    $filter_success = [
                        'id' => $item['id_success'],
                    ];
                    $success = Start_conversation::where($filter_success)->first();
                    $filter_error = [
                        'id' => $item['id_error'],
                    ];
                    $error = Start_conversation::where($filter_error)->first();
                    if ( $success ){
                        $data['array'][$key]['success'] = $success;
                    }
                    if ( $error ){
                        $data['array'][$key]['error'] = $error;
                    }
                }

            }
        }
        return $data;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return json_encode($value);
    }
}
