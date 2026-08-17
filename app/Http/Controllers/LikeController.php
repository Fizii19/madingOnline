<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Toggle the current user's like on a post.
     */
    public function toggle(Request $request, Post $post): RedirectResponse
    {
        $like = PostLike::where('post_id', $post->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($like) {
            $like->delete();
        } else {
            $post->likes()->create(['user_id' => $request->user()->id]);
        }

        return back();
    }
}
