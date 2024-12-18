<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use App\Models\Conversations;
use App\Services\Whatsapp\Utils\Contact;
use App\Services\Whatsapp\Utils\Message;
use Illuminate\Database\Eloquent\Casts\Json;
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

    public function send_message(Request $request){
        $contact = Contacts::find($request->contact_id);
        $conversation = Conversations::find($request->conversation_id);
        $message = [
            "name" => $contact->name,
            "wa_id" => $contact->phone_id,
            "type" => 'text',
            "body" => $request->message,
            "caption" => null,
            "data" => null,
            "status" => 'sent',

        ];
        $this->send($request, $message, $contact, $conversation);
    }
    public function send_message_test(Request $request)
    {

        //wamid.HBgMNTU0MTkyNTkxNjU1FQIAERgSMDlBNjZDMTEyOEYxNzIwRjc0AA==
        $contact = [
            'name' => 'test user name',
            'phone' => 16315551181,
            'phone_id' => 16315551181,
        ];
        $contact = $this->existsContact($contact);
        $message = [
            "name" => $contact->name,
            "wa_id" => $contact->phone_id,
            "type" => 'text',
            "body" => 'mensagem de teste 3, https://powinternet.com.br',
            "caption" => null,
            "data" => null,
            "status" => 'sent',

        ];
        $this->send($request, $message, $contact);
    }

    public function conversations(Request $request){
        $resume = $this->getResume($request->user());
        // dd($resume);
        return Inertia::render('Whatsapp/Whatsapp', ['conversations' => $resume]);
    }
    public function get_messages(Request $request) {
        $data = $this->getMessages($request['id']);
        return $data->toJson();
    }
}
