<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class PostController extends Controller
{
    public function index(){
        $posts = Post::all();
        return PostDetailResource::collection($posts->loadMissing('writer:id,username'));
    }

    public function show($id){
        $posts = Post::with('writer:id,username')->findOrFail($id);
        return new PostDetailResource($posts);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required',
        ]);

        $request['author'] = Auth::user()->id;
        $posts = Post::create($request->all());
        return new PostDetailResource($posts->loadMissing('writer:id,username'));
        
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required',
        ]);

        $posts = Post::findOrFail($id);
        $posts->update($request->all());
        return new PostDetailResource($posts->loadMissing('writer:id,username'));
    }

    public function destroy($id){
        $posts = Post::findOrFail($id);
        $posts->delete();
        return new PostDetailResource($posts->loadMissing('writer:id,username'));
    }
}
