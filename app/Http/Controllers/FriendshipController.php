<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendshipController extends Controller
{

    public function addFriend($friend_id)
    {
        $user = JWTAuth::user();
        if ($user->friendsTo()->where('friend_id', $friend_id)->exists()) {
            return response()->json('Friend request exists', 400);
        } else {
            $user->friendsTo()->attach($friend_id);
            return response()->json(['success' => true, 'message' => 'Friend request sent successfully']);
        }
    }

    public function removeFriend($friend_id)
    {
        $user = JWTAuth::user();
        if ($user->friendsTo()->where('friend_id', $friend_id)->exists()) {
            $user->friendsTo()->detach($friend_id);
            return response()->json(['success' => true, 'message' => 'Friend request removed successfully']);
        } else {
            return response()->json('Friend request not found', 400);

        }
    }

    public function acceptFriendRequest($friend_id)
    {
        $users = JWTAuth::user();
        DB::table('friendships')->where('user_id', $users->id)->where('friend_id', $friend_id)->update(['accepted' => '1']);
        return response()->json(['success' => true, 'message' => 'Accepted friend request']);
    }

    public function pendingFriendRequest()
    {
        $users = JWTAuth::user();
        return $users->pendingFriendsTo;
    }

    public function friendlist()
    {
        $user = JWTAuth::user();
        return $user->friends;
    }
}
