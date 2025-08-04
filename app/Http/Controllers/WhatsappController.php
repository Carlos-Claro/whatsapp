<?php

namespace App\Http\Controllers;

use App\Events\WhatsappCloseConversation;
use App\Events\WhatsappSendMessage;
use App\Models\Contacts;
use App\Models\Conversations;
use App\Models\Messages;
use App\Models\Start_conversation;
use App\Services\Whatsapp\Utils\Contact;
use App\Services\Whatsapp\Utils\Conversation;
use App\Services\Whatsapp\Utils\Message;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Netflie\WhatsAppCloudApi\WebHook;


class WhatsappController extends Controller
{
    use Contact, Message, Conversation;

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
            "wam_id" => 0,
            "type" => 'text',
            "body" => $request->message,
            "caption" => null,
            "data" => null,
            "status" => 'sent',
            'conversation_id' => $conversation->id,
        ];
        $data = [
            'type' => 'user',
            'message' => $message,
            'contact' => $contact,
            'user' => $request->user(),
        ];
        if ( isset($conversation) ){
            $data['conversation'] = $conversation;
        }
        $message = $this->saveMessage($data);
        WhatsappSendMessage::dispatch($message);
    }
    public function send_message_test(Request $request)
    {
        $newMessage = Start_conversation::where('tag', 'start')->first();
        dd($newMessage->answer);
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

        $conversation = Conversations::where('id', 35)->with(['members', 'contact', 'unReadMessages', 'lastMessageSystem'])->first();
        return Inertia::render('Whatsapp/Whatsapp');
    }
    public function get_resume(Request $request){
        $resume = $this->getResume($request->user());
        return $resume->toJson();
    }
    public function get_messages(Request $request) {
        $data = $this->getMessages($request['id']);
        return $data->toJson();
    }
    public function mark_message_as_read(Request $request) {
        $data = $this->markMessageAsRead($request['id']);
        // return $data->toJson();
    }
    public function close_conversation(Request $request) {
        $conversation = Conversations::where('id', $request['id'])->with(['members', 'contact', 'unReadMessages'])->first();
        $data = [
            'conversation' => $conversation,
            'pesquisa' => $request['pesquisa'],
        ];
        WhatsappCloseConversation::dispatch($data);
    }
}
