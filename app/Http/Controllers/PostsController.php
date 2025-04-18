<?php

namespace App\Http\Controllers;

use App\Models\Likes;
use App\Models\Post;
use App\Models\Comment;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\EditPostRequest;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\AddCommentRequest;
use App\Http\Requests\EditCommentRequest;
class PostsController extends Controller
{
    public function posts(Request $request)
    {
        return Post::posts($request);
    }

    public function createPost(CreatePostRequest $request)
    {
        return Post::createPost($request);
    }

    public function addComment(AddCommentRequest $request, $id)
    {
        return Comment::addComment($request, $id);
    }

    public function editComment(EditCommentRequest $request, $id)
    {
        return Comment::editComment($request, $id);
    }

    public function delComment(Request $request, $id)
    {
        return Comment::delComment($request, $id);
    }

    public function toggleLike(Request $request, $id)
    {
        return Likes::toggleLike($request, $id);
    }

    public function deletePost(Request $request, $id)
    {
        return Post::deletePost($request, $id);
    }

    public function updatePost(EditPostRequest $request, $id)
    {
        return Post::updatePost($request, $id);
    }

}
