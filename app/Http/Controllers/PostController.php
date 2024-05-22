<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $post = Post::latest()->paginate(5);
        if ($post->isEmpty()) {
            return new PostResource(false, 'Data Kosong', []); // Empty array for data
        } else {
            return new PostResource(true, 'List Data Post', $post);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'author' => 'required',
            'slug' => 'required',
            'body' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['message' => 'Data Gagal ditambah', 'errors' => $validator->errors()], 422);
        }

        $post = Post::create([
            'title' => $request->title,
            'author' => $request->author,
            'slug' => $request->slug,
            'body' => $request->body,
        ]);

        return new PostResource(true, 'Data Berhasil ditambah', $post);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = Post::find($id);
        if ($post) {
            return new PostResource(true, 'Detail Data Post', $post);
        } else {
            // Return a JSON response with an error message
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'author' => 'required',
            'slug' => 'required',
            'body' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = Post::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }
        $post->update([
            'title' => $request->title,
            'author' => $request->author,
            'slug' => $request->slug,
            'body' => $request->body,
        ]);

        return new PostResource(true, 'Data Berhasil diupdate', $post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if ($post) {
            $post->delete();
            return new PostResource(true, 'Data Berhasil dihapus', null);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data gagal dihapus'
            ], 500);
        }
    }
}
