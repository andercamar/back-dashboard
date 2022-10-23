<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardRequest;
use App\Models\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        if (Auth::check()){
            return Dashboard::paginate(perPage: 10);
        }
        return Dashboard::wherePermission(true)->paginate(perPage: 1);
    }

    public function store(DashboardRequest $request){
        return response()
            ->json(Dashboard::create($request->all()), 201);
    }

    public function show(int $dashboard){
        $data = Dashboard::find($dashboard);
        if ($data == null){
            return  response()->json(['message' => 'Dashboard not found'], status:404);
        }
        return $data;
    }
    public function update(Dashboard $dashboard, DashboardRequest $request){
        $dashboard->update($request->all());
        return $dashboard->fresh();
    }
    public function destroy(Dashboard $dashboard){
        $dashboard->delete();
        return response()->noContent();
    }
}
