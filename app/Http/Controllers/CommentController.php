<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index($post_id)
    {
        $post = Post::where('id', $post_id)->first();
        return $post->comment;
    }

    public function comment(Request $request, $post_id)
    {
        $validator = Validator::make($request->all(), [
            'comment_body' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $post = Post::where('id', $post_id)->first();
        if ($post) {
            $comment = Comment::create([
                'post_id' => $post->id,
                'user_id' => JWTAuth::user()->id,
                'comment_body' => $request->comment_body
            ]);
            return response()->json(compact('comment'), 201);
        } else {
            return response()->json('No such post', 400);
        }
    }

    public function delete($id)
    {
        $comment = Comment::find($id);
        if (isset($comment)) {
            $comment->delete();
            return response()->json(['success' => true, 'message' => 'Comment deleted successfully']);
        } else
            return response()->json('Cannot find selected comment', 400);
    }
}
