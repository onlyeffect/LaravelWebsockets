<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Auth;
use App\Events\NewComment;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $response = $post->comments()->with('user')->latest()->get();
        return response()->json($response);
    }

    public function store(Request $request, Post $post)
    {
        $this->validate($request, [
            'body' => 'required'
        ]);

        $comment = $post->comments()->create([
            'body' => $request->body,
            'user_id' => Auth::id(),
        ]);

        //$comment = Comment::where('id', $comment->id)->with('user')->first();
        $comment = $comment->load('user');

        broadcast(new NewComment($comment))->toOthers();

        return $comment->toJson();
    }
}
