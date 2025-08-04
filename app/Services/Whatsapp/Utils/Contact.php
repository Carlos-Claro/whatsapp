<?php

namespace App\Services\Whatsapp\Utils;

use App\Models\Contacts;

trait Contact {
    public function existsContact(array $data){
        $contact = Contacts::where('phone_id',$data['phone_id'])->first();
        if ( ! $contact ){
            $contact = Contacts::create($data);
        }
        return $contact;
    }
}
