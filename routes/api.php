<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ConditionController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//user
Route::post('login', [UserController::class, 'authenticate']);
Route::post('register', [UserController::class, 'register']);
Route::get('alluser', [UserController::class, 'index']);
Route::delete('/user/{id}', [UserController::class, 'delete']);
Route::patch('/user/update/{id}', [UserController::class, 'update']);
Route::patch('/user/updateprofile/{id}', [UserController::class, 'editProfile']);
Route::get('/user', [UserController::class, 'getAuthenticatedUser']);

//post
Route::delete('/post/delete/{id}', [PostController::class, 'delete']);
Route::get('post/{id}', [PostController::class, 'index']);
Route::get('allpost', [PostController::class, 'allPost']);
Route::get('specificpost/{id}', [PostController::class, 'findPost']);
Route::post('post/create/{id}', [PostController::class, 'create']);
Route::patch('/post/update/{id}', [PostController::class, 'update']);

//friend
Route::post('friend/add/{id}', [FriendshipController::class, 'addFriend']);
Route::post('friend/remove/{id}', [FriendshipController::class, 'removeFriend']);
Route::post('friend/accept/{id}', [FriendshipController::class, 'acceptFriendRequest']);
Route::get('friend', [FriendshipController::class, 'friendlist']);
Route::get('friend/pending', [FriendshipController::class, 'pendingFriendRequest']);

//chat
Route::get('/chat', [ChatController::class, 'index']);
Route::get('/messages', [ChatController::class, 'fetchMessages']);
Route::post('/messages', [ChatController::class, 'sendMessage']);

//condition
Route::get('condition/{id}', [ConditionController::class, 'index']);
Route::post('condition/create/{id}', [ConditionController::class, 'store']);

//medicine
Route::get('medicine', [MedicineController::class, 'index']);
Route::get('medicine/{id}', [MedicineController::class, 'index1']);
Route::post('medicine/store', [MedicineController::class, 'store']);
Route::patch('medicine/update/{id}', [MedicineController::class, 'update']);
Route::delete('medicine/delete/{id}', [MedicineController::class, 'delete']);
Route::patch('medicine/updatePhoto/{id}', [MedicineController::class, 'deletePhoto']);
// Route::patch('medicine/testupdatePhoto', [MedicineController::class, 'testdeletePhoto']);

//order
Route::get('order', [OrderController::class, 'index']);
Route::get('cartmedicine', [OrderController::class, 'medicineInCart']);
Route::get('medicinecheckout', [OrderController::class, 'medicineCheckout']);
Route::get('cart', [OrderController::class, 'cartStatusOrder']);
// Route::get('orderCheckout', [OrderController::class, 'checkoutStatusOrder']);
Route::post('order/addtocart/{id}', [OrderController::class, 'addToCart']);
Route::post('updateQty/{id}', [OrderController::class, 'updateQty']);
Route::delete('dltcartmed/{id}', [OrderController::class, 'dltFromCart']);
Route::post('checkout', [OrderController::class, 'checkout']);
