<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Competitor;
use App\Models\Route;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index($id) {
        $result=array();
        $id=htmlspecialchars($id);

        $competitors = Competitor::where('category_id', $id)
                            ->orderBy('startNumber', 'asc')
                            ->get();

        $allRoutes = Route::where('category_id', $id)
                    ->select('id')
                    ->get();

        $results=$competitors->map(function ($competitor) use ($allRoutes) {
            $competitor->routes=$allRoutes->map(function ($route) use ($competitor) {
                $route->result = Result::where('competitor_id', $competitor->id)
                                    ->where('route_id', $route->id)
                                    ->first();

                if(!is_object($route->result)) {
                    $route->result = new Result();
                    $route->result->height = 0;
                    $route->result->top = 0;
                    $route->result->attempt = 0;
                }

                return $route;
            });

            $competitor->routes = $competitor->routes->toArray();

            return $competitor;
        });

        return response()->json($results);
    }

    public function get($id) {
        $result = Result::findOrFail($id);
        return response()->json($result);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'competitors_id'  => 'required|exists:competitors,id',
            'route_id'  => 'required|exists:route,id',
            'height'  => 'required|numeric',
            'attempt' => 'required|digits_between:0,1',
            'top' => 'required|digits_between:0,1',
            'note' => 'string'
        ]);

        $result = new Result;
        $result->competitor_id = $request->input('competitors_id');
        $result->route_id = $request->input('route_id');
        $result->height = $request->input('height');
        $result->attempt = $request->input('attempt');
        $result->top = $request->input('top');
        $result->note = $request->input('note');

        if(!$result->save()) {
            App::abort(500, 'Error saving results');
        }

        return response()->json("success");
    }

    public function update(Request $request) {
        $catId = 0;

        foreach ($request->all() as $item) {
            $catId=intval($item["category_id"]);

            foreach ($item["routes"] as $route) {
                $resultTemp = $route["result"];

                if(isset($resultTemp['id'])) {
                    $result = Result::find($resultTemp['id']);
                } else {
                    $result = new Result;
                }

                $result->competitor_id = $item["id"];
                $result->route_id = $route["id"];
                $result->height = floatval($resultTemp['height']);
                $result->attempt = intval((boolean)$resultTemp['attempt']);
                $result->top = intval((boolean)$resultTemp['top']);
                $result->note = "";

                if(!$result->save()) {
                    App::abort(500, 'Error updating results');
                }
            }
        }

        if($catId > 0) {
            $this->calculateRanking($catId);
        }

        return response()->json("success");
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:results'
        ]);

        $results = Results::find($request->input('id'));

        if(!$results->delete()) {
            App::abort(500, 'Error deleting results');
        }

        return response()->json("success");
    }

    private function calculateRanking($catId) {
        $competitors = Competitor::select('id')
                            ->where('category_id', $catId)
                            ->orderBy('startNumber', 'asc')
                            ->get();

        $total=$competitors->toArray();
        $total=collect($total);
        $total=$total->map(function ($item, $key) {
            return array_merge($item, array(
                "points" => 0,
                "rank" => 0
            ));
        });

        $allRoutes = Route::select('id')
                    ->where('category_id', $catId)
                    ->get();

        $routesCount=count($allRoutes->all());

        for ($i=0; $i < $routesCount; $i++) {
            $competitor=$competitors->map(function ($competitor) use ($allRoutes, $i) {
                $result = Result::where('competitor_id', $competitor->id)
                                    ->where('route_id', $allRoutes[$i]->id)
                                    ->first();

                if($result->attempt == 1) {
                    $result->height +=0.000001;
                }

                return [
                    "id" => $result->id,
                    "competitor" => $competitor->id,
                    'height' => $result->height
                ];
            });

            //results are now sorted by height asc
            $competitor = $competitor->sortByDesc("height");
            $competitor = array_values($competitor->toArray());

            //calculating points depending on height
            for ($j=0; $j < count($competitor); $j++) {
                if(($j+1) == count($competitor)) {
                    $k=$j-1;
                } else {
                    $k=$j+1;
                }

                //check if competitors have same height
                if($competitor[$j]['height'] == $competitor[$k]['height']) {
                    $points=$k;
                    $l=1;

                    //echo "!!".$k."**";

                    do {
                        $k++;
                        //TODO FIND LOOP
                        if(count($competitor) == $k) {
                            break;
                        }

                        $points+=$k;
                        $l++;
                    } while($competitor[$j]['height'] == $competitor[$k]['height']);

                    /*echo $k."--";
                    print_r($points);
                    echo "/";
                    print_r($l);*/

                    $points = $points / $l;

                    /*echo "/";
                    print_r($points);*/

                    for ($m=$j; $m < $k; $m++) {
                        $competitor[$m]['rank']=$j+1;
                        $competitor[$m]['points']=$points;
                    }

                    $j=$k-1;
                }
                //they don't
                else {
                    $competitor[$j]['rank']=$j+1;
                    $competitor[$j]['points']=$j+1;
                }
            }

            //inserting data into table
            foreach ($competitor as $data) {
                $result = Result::find($data['id']);
                $result->points = floatval($data['points']);
                $result->ranking = floatval($data['rank']);

                if(!$result->save()) {
                    App::abort(500, 'Error updating results');
                }
            }
        }


        //TOTAL RESULT
        $results=$competitors->map(function ($competitor) use ($allRoutes, $routesCount) {
            $points=$allRoutes->map(function ($route) use ($competitor) {
                $route->result = Result::where('competitor_id', $competitor->id)
                                    ->where('route_id', $route->id)
                                    ->first();

                return $route->result->points;
            });

            $total=0;

            for ($i=0; $i < $routesCount; $i++) {
                $total+=$points[$i];
            }

            $total=pow($total, $routesCount);

            return [
                "id" => $competitor->id,
                "total" => $total,
                "ranking" => 0
            ];
        });

        //sort total points
        $results=$results->sortBy("total");
        $results = array_values($results->toArray());

        for ($i=0; $i < count($results); $i++) {
            if($i == 0) {
                $results[$i]['ranking']=$i+1;
            } else {
                if($results[$i]['total'] == $results[($i-1)]['total']) {
                    $results[$i]['ranking'] = $results[($i-1)]['ranking'];
                } else {
                    $results[$i]['ranking']=$i+1;
                }
            }

            $data=$results[$i];

            $competitor = Competitor::find($data['id']);
            $competitor->points = floatval($data['total']);
            $competitor->ranking = $data['ranking'];

            if(!$competitor->save()) {
                App::abort(500, 'Error updating results');
            }
        }
    }
}
