<?php

namespace App\Http\Controllers;

use App\Models\Post;
use http\Env\Response;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function posts(Request $request)
    {
        $loggedInUser  = $request->user();


        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'User  not authenticated'], 401);
        }

        $posts = Post::with('user')->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts,
            'profileLink' => url("/profile/{$loggedInUser ->id}"),
        ], 200);
    }



    public function createPost(Request $request)
    {

        $loggedInUser  = $request->user();


        if (!$loggedInUser ) {
            return response()->json(['status' => 'error', 'message' => 'User  not authenticated'], 401);
        }


        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);


        $post = Post::create([
            'user_id' => $loggedInUser ->id,
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'photo' => $this->uploadImage($request),

        ]);

        return response()->json(['status' => 'success', 'post' => $post], 201);
    }


    private function uploadImage(Request $request)
    {
        if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('images/posts', 'public');
            return $imagePath;
        }
        return null;
    }

}
