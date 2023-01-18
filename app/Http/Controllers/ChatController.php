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

    public function sendPrivateMessage(Request $request, $id)
    {
        // if(request()->has('file')){
        //     $message=Message::create([
        //         'user_id' => request()->user()->id,
        //         'receiver_id' => $user->id
        //     ]);
        // }else{
        $input=$request->all();
        $input['receiver_id']=$id;
        $message=JWTAuth::user()->messages()->create($input);

        broadcast(new PrivateMessageSent($message->load('user')))->toOthers();

        return response(['status' => 'Message private sent successfully', 'message' => $message]);
    }

    public function privateMessages($id)
    {
        // $privateCommunication = Message::with('user')
        //     ->where(['user_id' => JWTAuth::user()->id(), 'receiver_id' => $id])
        //     ->orWhere(function ($query) use ($id) {
        //         $query->where(['user_id' => $id, 'receiver_id' => JWTAuth::user()->id()]);
        //     })
        //     ->get();
        $user = User::find($id)->name;
        // dd($user);
        $privateCommunication = Message::where(['user_id' => JWTAuth::user()->id, 'receiver_id' => $id])
        ->orWhere(function ($query) use ($id) {
            $query->where(['user_id' => $id, 'receiver_id' => JWTAuth::user()->id]);
        })
        ->get();
        foreach ($privateCommunication as $m) {
            $m->setAttribute('user_name', $m->user->userName());
            $m->setAttribute('avatar', $m->user->avatar());
        }
        return $privateCommunication;
    }
}
