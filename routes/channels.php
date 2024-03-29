<?php

use Illuminate\Support\Facades\Broadcast;
use Tymon\JWTAuth\Facades\JWTAuth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('chat', function ($user) {
    return JWTAuth::check();
});

Broadcast::channel('privatechat.{receiverid}', function ($user, $receiverid) {
    // return JWTAuth::check();
    return ['id' => $user->id, 'name' => $user->name];

});

Broadcast::channel('publicchat', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];

});

Broadcast::channel('plchat', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
    // if (JWTAuth::check()) {
    //     return $user;
    // }
});

Broadcast::channel('agora-online-channel', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});
