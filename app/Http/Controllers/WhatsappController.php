<?php

namespace App\Http\Controllers;

use App\Services\Whatsapp\Utils\Contact;
use App\Services\Whatsapp\Utils\Message;
use Illuminate\Http\Request;
use Netflie\WhatsAppCloudApi\WebHook;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

class WhatsappController extends Controller
{
    use Contact, Message;
    private WhatsAppCloudApi $whatsapp;
    public function __construct(){
        $this->whatsapp = new WhatsAppCloudApi([
            'from_phone_number_id' => env('WHATSAPP_FROM_PHONE_NUMBER_ID'),
            'access_token' => env('WHATSAPP_TOKEN'),
        ]);
    }
    public function set(Request $request){
        $webhook = new WebHook();
        echo $webhook->verify($_GET, 'powi0000');
    }

    public function send_message(Request $request){
        $contact = [
            'name' => 'Eu',
            'phone' => 5541992591655,
            'phone_id' => 5541992591655,
        ];
        $contact = $this->existsContact($contact);
        $data = [
            "name" => 'Eu',
            "wa_id" => 5541992591655,
            "type" => 'text',
            "body" => 'mensagem de teste, https://powinternet.com.br',
            "caption" => null,
            "data" => null,
            "status" => 'delivered',
            "contact_id" => $contact->id,
        ];
        $response = $this->whatsapp->sendTextMessage($data['wa_id'], $data['body']. true);
        $decoded = $response->decodedBody();
        dump($response);
        $data['wam_id'] = $decoded['messages'][0]['id'];
        $item = $this->saveMessage($data);
        dd($item);
    }
}
