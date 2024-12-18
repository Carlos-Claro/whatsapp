<?php

namespace App\Http\Controllers;

use App\Services\Whatsapp\Utils\Contact;
use App\Services\Whatsapp\Utils\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;
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

    public function send_message(Request $request)
    {

        //wamid.HBgMNTU0MTkyNTkxNjU1FQIAERgSMDlBNjZDMTEyOEYxNzIwRjc0AA==
        $contact = [
            'name' => 'Eu',
            'phone' => 554192591655,
            'phone_id' => 554192591655,
        ];
        $contact = $this->existsContact($contact);
        $message = [
            "name" => 'Eu',
            "wa_id" => 554192591655,
            "type" => 'text',
            "body" => 'mensagem de teste 2, https://powinternet.com.br',
            "caption" => null,
            "data" => null,
            "status" => 'sent',
            "contact_id" => $contact->id,
        ];
        $this->send($request, $message, $contact);

    }

    public function conversations(Request $request){
        $resume = $this->getResume($request->user());
        // dd($resume);
        return Inertia::render('Whatsapp/Whatsapp', ['conversations' => $resume]);
    }
}
