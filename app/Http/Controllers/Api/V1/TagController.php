<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Validator;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $tags = $user->tags()->get();
        return response()->json($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        $tag = $user->tags()->findOrFail($id);
        if (auth()->id() !== $tag->user_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        //collect task
        $tag = Tag::findOrFail($id);
        $tag->load('tasks');
        return response()->json($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Pastikan task ini milik pengguna yang login
        $user = auth()->user();
        $tag = $user->tags()->findOrFail($id);
        if (auth()->id() !== $tag->user_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        // dd($request->all());

        // Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $tag->update($request->only(['title']));

        $data = $tag;
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Pastikan tag ini milik pengguna yang login
        $user = auth()->user();
        $tag = $user->tags()->findOrFail($id);
        if (auth()->id() !== $tag->user_id) {
            return response()->json([
                'message' =>
                    'Unauthorized'
            ], 403);
        }

        //todo: user dapat memilih, task yang terkait dengan kategori dihapus juga atau engga

        $tag->delete();
        return response()->json(['message' => 'Tag deleted successfully']);
    }
}
