<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Log;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function get($id) {
        $category = Category::findOrFail(htmlspecialchars($id));
        return response()->json($category);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'competition_id'  => 'required|exists:competition,id',
            'name'  => 'required|max:200',
            'yearFrom' => 'required|date_format:Y',
            'yearTo' => 'required|date_format:Y'
        ]);

        $category = new Category;
        $category->competition_id = $request->input('competition_id');
        $category->name = $request->input('name');
        $category->yearFrom = $request->input('yearFrom');
        $category->yearTo = $request->input('yearTo');

        if(!$category->save()) {
            App::abort(500, 'Error saving category');
        }

        return response()->json("success");
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:category',
            'competition_id'  => 'required|exists:competition,id',
            'name'  => 'required|max:200',
            'yearFrom' => 'required|date_format:Y',
            'yearTo' => 'required|date_format:Y'
        ]);

        $category = Category::find($request->input('id'));
        $category->competition_id = $request->input('competition_id');
        $category->name = $request->input('name');
        $category->yearFrom = $request->input('yearFrom');
        $category->yearTo = $request->input('yearTo');


        if(!$category->save()) {
            App::abort(500, 'Error updating category');
        }

        return response()->json("success");
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:category'
        ]);

        $category = Category::find($request->input('id'));

        if(!$category->delete()) {
            App::abort(500, 'Error deleting category');
        }

        return response()->json("success");
    }
}
