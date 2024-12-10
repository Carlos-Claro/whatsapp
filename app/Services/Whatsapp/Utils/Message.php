<?php
namespace App\Services\Whatsapp\Utils;

use App\Models\Messages;
trait Message{
    public function existsMessageInDatabase ($id){
        return Messages::where('wam_id', $id)
                        ->orderBy('created_at', 'desc')
                        ->limit(1)
                        ->get();
    }
    public function saveMessage($data){
        return Messages::create($data)->first();
    }
}
