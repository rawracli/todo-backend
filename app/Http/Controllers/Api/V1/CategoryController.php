<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $categories = Category::where('user_id', $user->id)->with('tasks')->get();

        return response()->json($categories);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        //todo: bikin free limitation untuk jumlah pembuatan kategori
        // $plan = $user->plan;
        // // Validasi jumlah task
        // if ($plan && $plan->task_limit > 0 && $user->tasks()->count() >= $plan->task_limit) {
        //     return response()->json([
        //         'message' => 'You have reached the maximum number of tasks allowed for your plan.'
        //     ], 429); // Too Many Requests
        // }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = $user->categories()->create([
            'title' => $request->title
        ]);

        $data = $category;
        return response()->json($data, 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Category::with('tasks')->findOrFail($id);

        $data = [$order];

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Pastikan task ini milik pengguna yang login
        $user = auth()->user();
        $category = $user->categories()->findOrFail($id);
        if (auth()->id() !== $category->user_id) {
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

        $category->update($request->only(['title']));
        $data = $category;
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Pastikan category ini milik pengguna yang login
        $user = auth()->user();
        $category = $user->categories()->findOrFail($id);
        if (auth()->id() !== $category->user_id) {
            return response()->json([
                'message' =>
                    'Unauthorized'
            ], 403);
        }

        //todo: user dapat memilih, task yang terkait dengan kategori dihapus juga atau engga

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);

    }
}
