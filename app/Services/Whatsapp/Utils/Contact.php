<?php

namespace App\Services\Whatsapp\Utils;

use App\Models\Contacts;

trait Contact {
    public function existsContact(array $data){
        $contact = Contacts::where('phone_id',$data['phone_id'])->get();
        if ( $contact->isEmpty() ){
            $contact = Contacts::create($data)->get();
        }
        return $contact[0];
    }

}
