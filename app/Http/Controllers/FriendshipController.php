<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $frequest = DB::table('friendships')->where('user_id', $friend_id)->where('friend_id', $user->id)->where('accepted', '0');
        if ($user->friendsTo()->where('friend_id', $friend_id)->exists()) {
            $user->friendsTo()->detach($friend_id);
            return response()->json(['success' => true, 'message' => 'Friend request removed successfully']);
        } else if (isset($frequest)) {
            $frequest->delete();
            return $user->pendingFriendsFrom;
        } else {
            return response()->json('Friend request not found', 400);
        }
    }

    public function acceptFriendRequest($friend_id)
    {
        $user = JWTAuth::user();
        DB::table('friendships')->where('user_id', $friend_id)->where('friend_id', $user->id)->update(['accepted' => '1']);
        // return response()->json(['success' => true, 'message' => 'Accepted friend request']);
        return $user->pendingFriendsFrom;
    }

    public function pendingFriendRequestTo()
    {
        $users = JWTAuth::user();
        return $users->pendingFriendsTo;
    }

    public function pendingFriendRequestFrom()
    {
        $users = JWTAuth::user();
        return $users->pendingFriendsFrom;
    }

    public function friendlist()
    {
        $user = JWTAuth::user();
        return $user->friends;
    }

    public function friendlistmsg()
    {
        $user = JWTAuth::user();
        return $user->friends;
    }

    public function searchFriend(Request $request)
    {
        //friendstatus=1, print 'send msg'
        //friendstatus=2, print 'friend request sent'
        //friendstatus=3, print 'respond friend request'
        //friendstatus=0, print 'add friend'
        $userid = JWTAuth::user()->id;
        $user = JWTAuth::user();
        $q = $request->get('input');
        if (!$q) {

            return response()->json('Please search something!', 400);
        };
        $users = User::where('name', 'LIKE', '%' . $q . '%')->orWhere('email', 'LIKE', '%' . $q . '%')->where('role_id', '!=', 3)->get();
        $new=$users->where('role_id', '!=', 3);
        $newusers = $new->filter(function ($item) {
            $userid = JWTAuth::user()->id;
            return $item->id != $userid;
        })->values();
        if (count($newusers) > 0) {
            foreach ($newusers as $result) {
                $result->setAttribute('friendstatus', '0');
                foreach ($user->friends as $userfriend) {
                    if ($result->id == $userfriend->id)
                        $result->setAttribute('friendstatus', '1');
                }
                foreach ($user->pendingFriendsTo as $userpendingfriend) {
                    if ($result->id == $userpendingfriend->id)
                        $result->setAttribute('friendstatus', '2');
                }
                foreach ($user->pendingFriendsFrom as $userpendingfriend1) {
                    if ($result->id == $userpendingfriend1->id)
                        $result->setAttribute('friendstatus', '3');
                }
            }
            // dd($user->pendingFriendsTo[0]->id);
            return $newusers;
        } else return response()->json('No Details found. Try to search again !', 400);
        // return $user->pendingFriendsTo[0]->id;
    }
}
