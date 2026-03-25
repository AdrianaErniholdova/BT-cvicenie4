<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Note;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*$categories = DB::table('categories')
            ->orderBy('name', 'asc')
            ->get();*/
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return response()->json(['categories' => $categories], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'color' => ['required', 'string', 'max:255'],
        ]);

        $category = Category::create([
            'name' => $validated['name'],
        ]);

        return response()->json([
            'message' => 'Kategória bola úspešne vytvorená.',
            'category' => $category
        ], Response::HTTP_CREATED);

        /*DB::table('categories')->insert([
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $category = Category::create([
            'name' => $request->name,
            'color' => $request->color
        ]);*/
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        /*$category = DB::table('categories')
            ->where('id', $id)
            ->first();*/
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Kategória nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['category' => $category], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Kategória nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', 'unique:categories,name,' . $id],
            'color' => ['sometimes', 'string', 'max:255'],
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Kategória bola úspešne aktualizovaná.',
            'category' => $category
        ], Response::HTTP_OK);

        /*$category = DB::table('categories')
            ->where('id', $id)
            ->first();*/

        /*DB::table('categories')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'updated_at' => now(),
            ]);*/

        /*$category = Category::find($id);

        $category->update([
            'name' => $request->name,
            'color' => $request->color
        ]);*/
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /*$category = DB::table('categories')
            ->where('id', $id)
            ->first();*/
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Kategória nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        //DB::table('categories')->where('id', $id)->delete();
        $category->delete();

        return response()->json([
            'message' => 'Kategória bola úspešne odstránená.'
        ], Response::HTTP_OK);
    }
}
