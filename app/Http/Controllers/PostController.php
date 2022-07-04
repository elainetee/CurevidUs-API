<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    //$user_id = $request->user()->id;

    public function index($user_id)
    {
        $posts = Post::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
        // $posts->duration=$posts->created_at->diffForHumans();
        foreach ($posts as $p) {
            $p->setAttribute('duration', $p->created_at->diffForHumans());
            $p->setAttribute('user_name', $p->user->userName());
        }
        return $posts;
    }

    public function findPost($id)
    {
        
        $post = Post::where('id', $id)->first();
        $post->duration = $post->created_at->diffForHumans();
        return $post;
    }

    public function allPost()
    {
        $posts= Post::get();
        return $posts;
    }

    public function create(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            // 'visibility' => 'required|string',
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors()->toJson(), 400);
        }
        // if (isset($user)) {
        $post = Post::create([
            'content' => $request->get('content'),
            'visibility' => $request->get('visibility'),
            'user_id' => $user_id,
        ]);
        $post->duration = $post->created_at->diffForHumans();

        return response()->json(compact('post'), 201);
        // } else return response()->json('Fail to create company', 400);
    }

    public function update(Request $request, $post_id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            // 'visibility' => 'required|string',
        ]);
        if ($validator->fails()) {

            return response()->json($validator->errors()->toJson(), 400);
        }
        $post = Post::find($post_id);
        $post->update($request->all());

        return $post;
    }

    public function delete($id)
    {
        $post = Post::find($id);
        if (isset($post)) {
            $post->delete();

            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        } else

            return response()->json('Cannot find selected user', 400);
    }
}
