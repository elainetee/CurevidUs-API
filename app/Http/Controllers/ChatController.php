<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Events\MessageSent;
use App\Events\PrivateMessageSent;

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

    public function sendPrivateMessage(Request $request,User $user)
    {
        if(request()->has('file')){
            $message=Message::create([
                'user_id' => request()->user()->id,
                'receiver_id' => $user->id
            ]);
        }else{
            $input=$request->all();
            $input['receiver_id']=$user->id;
            $message=auth()->user()->messages()->create($input);
        }

        broadcast(new PrivateMessageSent($message->load('user')))->toOthers();
        
        return response(['status'=>'Message private sent successfully','message'=>$message]);

    }

    public function privateMessages(User $user)
    {
        $privateCommunication= Message::with('user')
        ->where(['user_id'=> auth()->id(), 'receiver_id'=> $user->id])
        ->orWhere(function($query) use($user){
            $query->where(['user_id' => $user->id, 'receiver_id' => auth()->id()]);
        })
        ->get();

        return $privateCommunication;
    }
}
