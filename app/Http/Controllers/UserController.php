<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $user = JWTAuth::user();
        $user = User::get(['id','role_id', 'name', 'email','emergency_contact_person_id','quarantine_status','vac_status']);
        return $user;
    }

    public function delete($id)
    {
        $user = User::find($id);
        if (isset($user)) {
            $user->delete();

            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        } else 
        
        return response()->json('Cannot find selected user', 400);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255' . $id,
            'email' => 'required|string|email|max:255',
            'role_id' => 'required' ,
            'tel_no' => 'required' ,
        ]);
        if ($validator->fails()) {

            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::find($id);
        $user->update($request->all());

        return response()->json(compact('user'), 201);
    }

    public function authenticate(Request $request)
    {

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {

                return response()->json(['error' => 'Invalid credentials'], 400);
            }
        } catch (JWTException $e) {

            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(compact('token'), 201);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required',
            'tel_no' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role_id' => $request->get('role_id'),
            'tel_no' => $request->get('tel_no'),
        ]);
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {

                return response()->json(['user_not_found'], 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getCode());
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getCode());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getCode());
        }
        // return response()->json(compact('user'));

        return $user;
    }

    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {

            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ]);
        }
    }
}