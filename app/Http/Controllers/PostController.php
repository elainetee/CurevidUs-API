<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

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

    public function allVisiblePost()
    {
        $posts = Post::orderBy('id', 'DESC')->get();
        $newPosts = $posts->filter(function ($item) {
            $userid = JWTAuth::user()->id;
            return $item->user_id != $userid;
        })->values();
        $filteredpost = $newPosts->whereNotIn('visibility', 'hidden');
        foreach ($filteredpost as $p) {
            $p->setAttribute('duration', Carbon::parse($p['created_at'])->diffForHumans());
            $p->setAttribute('user_name', $p->user->userName());
        }
        return $filteredpost->toQuery()->simplePaginate(5);
    }

    public function allHiddenPost()
    {
        $posts = Post::orderBy('id', 'DESC')->get();
        $newPosts = $posts->filter(function ($item) {
            $userid = JWTAuth::user()->id;
            return $item->user_id != $userid;
        })->values();
        $filteredpost = $newPosts->whereIn('visibility', 'hidden');
        foreach ($filteredpost as $p) {
            $p->setAttribute('duration', Carbon::parse($p['created_at'])->diffForHumans());
            $p->setAttribute('user_name', $p->user->userName());
        }
        return $filteredpost;
    }

    public function publicPost()
    {
        $user = JWTAuth::user();
        $friends = $user->friends->pluck('id');
        $posts = Post::orderBy('id', 'DESC')->get();
        $newPosts = $posts->filter(function ($item) {
            $userid = JWTAuth::user()->id;
            return $item->user_id != $userid;
        })->values();
        // $id = $helpers->lists('id');
        $filteredpost = $newPosts->whereNotIn('user_id', $friends)->whereNotIn('visibility', 'hidden');
        // dd($filteredpost);
        foreach ($filteredpost as $p) {
            $p->setAttribute('duration', Carbon::parse($p['created_at'])->diffForHumans());
            $p->setAttribute('user_name', $p->user->userName());
        }
        return $filteredpost;
    }

    public function friendPost()
    {
        $user = JWTAuth::user();
        $friends = $user->friends->pluck('id');
        $posts = Post::orderBy('id', 'DESC')->get();
        $newPosts = $posts->filter(function ($item) {
            $userid = JWTAuth::user()->id;
            return $item->user_id != $userid;
        })->values();
        $filteredpost = $newPosts->whereIn('user_id', $friends);
        foreach ($filteredpost as $p) {
            $p->setAttribute('duration', Carbon::parse($p['created_at'])->diffForHumans());
            $p->setAttribute('user_name', $p->user->userName());
        }
        return $filteredpost;
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
            'visibility' => 'public',
            // 'visibility' => $request->get('visibility'),
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
        $post->update([
            'content' => $request->get('content'),
        ]);

        return $post;
    }

    public function delete($id)
    {
        $post = Post::find($id);
        if (isset($post)) {
            $post->delete();
            return response()->json(['success' => true, 'message' => 'Post deleted successfully']);
        } else

            return response()->json('Cannot find selected post', 400);
    }

    public function hide($id)
    {
        $post = Post::find($id);
        if (isset($post)) {
            if ($post->visibility == 'public') {
                $post->update([
                    'visibility' => 'hidden',
                ]);
                return response()->json(['success' => true, 'message' => 'Post hidden successfully']);
            } else {
                $post->update([
                    'visibility' => 'public',
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Post is changed to public successfully']);
        } else

            return response()->json('Cannot find selected post', 400);
    }
}
