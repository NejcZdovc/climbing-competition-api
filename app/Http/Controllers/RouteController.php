<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index($id) {
        $routes = Route::where('category_id', htmlspecialchars($id))
               ->get();
        return response()->json($routes);
    }

    public function get($id) {
        $route = Route::findOrFail($id);
        return response()->json($route);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'category_id'  => 'required|exists:category,id',
            'name'  => 'required|max:100',
            'referee'  => 'required|max:100',
            'routesetter'  => 'required|max:100',
            'time' => 'required|integer'
        ]);

        $route = new Route;
        $route->category_id = $request->input('category_id');
        $route->name = $request->input('name');
        $route->referee = $request->input('referee');
        $route->routesetter = $request->input('routesetter');
        $route->time = $request->input('time');

        if(!$route->save()) {
            App::abort(500, 'Error saving route');
        }

        return response()->json("success");
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:route'
        ]);

        $route = Route::find($request->input('id'));
        $route->category_id = $request->input('category_id');
        $route->name = $request->input('name');
        $route->referee = $request->input('referee');
        $route->routesetter = $request->input('routesetter');
        $route->time = $request->input('time');

        if(!$route->save()) {
            App::abort(500, 'Error updating route');
        }

        return response()->json("success");
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:route'
        ]);

        $route = Route::find($request->input('id'));

        if(!$route->delete()) {
            App::abort(500, 'Error deleting route');
        }

        return response()->json("success");
    }
}
