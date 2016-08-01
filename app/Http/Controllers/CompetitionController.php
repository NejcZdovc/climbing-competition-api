<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function index() {
        $competitions = Competition::all();
        return response()->json($competitions);
    }

    public function get($id) {
        $competition = Competition::findOrFail($id);
        return response()->json($competition);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'name'  => 'required|max:45',
            'date' => 'required|date_format:Y-m-d',
            'location'  => 'required|max:45',
            'referee' => 'required|max:100'
        ]);

        $competition = new Competition;
        $competition->name = $request->input('name');
        $competition->dateOf = $request->input('date');
        $competition->location = $request->input('location');
        $competition->referee = $request->input('referee');

        if(!$competition->save()) {
            App::abort(500, 'Error saving competition');
        }

        return response()->json("success");
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:competition'
        ]);

        $competition = Competition::find($request->input('id'));
        $competition->name = $request->input('name');
        $competition->dateOf = $request->input('date');
        $competition->location = $request->input('location');
        $competition->referee = $request->input('referee');

        if(!$competition->save()) {
            App::abort(500, 'Error updating competition');
        }

        return response()->json("success");
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:competition'
        ]);

        $competition = Competition::find($request->input('id'));

        if(!$competition->delete()) {
            App::abort(500, 'Error deleting competition');
        }

        return response()->json("success");
    }
}
