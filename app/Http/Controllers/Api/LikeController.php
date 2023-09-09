<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Http\Resources\LikesCollection;
use App\Http\Resources\UsersCollection;
use App\Http\Resources\PostsCollection;

class LikeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['post_id' => 'required']);

        try {
            $like = new Like;

            $like->user_id = auth()->user()->id;
            $like->post_id = $request->input('post_id');
            $like->save();

            $post = Post::find($like->post_id);
            if ($post) {
                $receiver = $post->user; 
                $amountToAdd = 1; 
                $receiver->increment('currency', $amountToAdd);
                }

            return response()->json([
                'like' => [
                    'id' => $like->id,
                    'post_id' => $like->post_id,
                    'user_id' => $like->user_id
                ],
                'success' => 'OK'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $like = Like::find($id);
            if (count(collect($like)) > 0) {
                $like->delete();
            }

            $post = Post::find($like->post_id);
                if ($post) {
                    $receiver = $post->user; 
                    $amountToAdd = -1; 
                    $receiver->increment('currency', $amountToAdd);
                }

            return response()->json([
                'like' => [
                    'id' => $like->id,
                    'post_id' => $like->post_id,
                    'user_id' => $like->user_id
                ],
                'success' => 'OK'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
