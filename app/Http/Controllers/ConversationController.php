<?php

namespace App\Http\Controllers;

use App\Models\Conversations;
use Illuminate\Http\Request;

class ConversationController extends Controller
{

    public function get_conversation(Request $request)
    {
        $conversation = Conversations::where('id',$request->id)->with(['company', 'contact', 'user', 'lastMessage'])->first();
        // dd($conversation->company->logo_address);
        return response()->json($conversation);
    }
}
