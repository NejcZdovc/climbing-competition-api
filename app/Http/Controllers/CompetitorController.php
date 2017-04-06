<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use Illuminate\Http\Request;

class CompetitorController extends Controller
{
    public function index($id) {
        $competitors = Competitor::where('category_id', htmlspecialchars($id))
                                ->get();
        return response()->json($competitors);
    }

    public function get($id) {
        $competitor = Competitor::findOrFail($id);
        return response()->json($competitor);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'category_id'  => 'required|exists:category,id',
            'firstname'  => 'required|max:200',
            'lastname'  => 'required|max:200',
            'birth' => 'required|date_format:Y-m-d',
            'ranking' => 'numeric',
            'startNumber' => 'numeric',
            'club' => 'required|max:100',
        ]);

        $competitor = new Competitor;
        $competitor->category_id = $request->input('category_id');
        $competitor->firstname = $request->input('firstname');
        $competitor->lastname = $request->input('lastname');
        $competitor->birth = $request->input('birth');
        $competitor->ranking = $request->input('ranking');
        $competitor->startNumber = $request->input('startNumber');
        $competitor->club = $request->input('club');

        if(!$competitor->save()) {
            App::abort(500, 'Error saving competitor');
        }

        return response()->json("success");
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:competitor'
        ]);

        $competitor = Competitor::find($request->input('id'));
        $competitor->category_id = $request->input('category_id');
        $competitor->firstname = $request->input('firstname');
        $competitor->lastname = $request->input('lastname');
        $competitor->birth = $request->input('birth');
        $competitor->ranking = $request->input('ranking');
        $competitor->startNumber = $request->input('startNumber');
        $competitor->club = $request->input('club');

        if(!$competitor->save()) {
            App::abort(500, 'Error updating competitor');
        }

        return response()->json("success");
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:competitor'
        ]);

        $competitor = Competitor::find($request->input('id'));

        if(!$competitor->delete()) {
            App::abort(500, 'Error deleting competitor');
        }

        return response()->json("success");
    }
}
