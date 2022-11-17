<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function index()
    {
        // $message = Message::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
        $messages = Message::get();
        foreach ($messages as $m) {
        $m->setAttribute('user_name', $m->user->userName());
        }
        return $messages;
    }

    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    public function sendMessage(Request $request)
    {
        $user = JWTAuth::user();
        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);
        broadcast(new MessageSent($user, $message))->toOthers();
        return response()->json([
            'status' => 'Message sent!'
        ]);
    }
}
