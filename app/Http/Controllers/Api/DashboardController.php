<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\DashboardRequest;
use App\Models\Dashboard;
use App\Models\Department;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\DashboardResource;

class DashboardController extends BaseController
{
    public function index(Authenticatable $user){
        if ($user->tokenCan('is_admin')){
            $dashboards = Dashboard::with("departments");
            $data = DashboardResource::collection($dashboards->get());
            return $this->sendResponse($data, 'Return Successfully 1', 200);
        }
        $ids = User::find($user->id)->departments()->allRelatedIds();
        $dashboards = Dashboard::select("dashboards.id","dashboards.name","dashboards.description","dashboards.image")
            ->leftJoin('department_dashboard','dashboard_id','dashboards.id')
                ->whereIn('department_dashboard.department_id', $ids)
                    ->orWhere('dashboards.permission','=',true)
                        ->distinct()
                            ->get();
        $data = DashboardResource::collection($dashboards);
        return $this->sendResponse($data, 'Return Successfully', 200);
    }

    public function store(DashboardRequest $request, Authenticatable $user){
        if ($user->tokenCan('is_admin')){
            $data = Dashboard::create($request->all());
            $data->departments()->attach($request->input('departments'));
            return $this->sendResponse($data, 'Create Successfully', 201);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }

    public function show(int $dashboard,Authenticatable $user){
        if ($user->tokenCan('is_admin')){
            $query = Dashboard::find($dashboard);
            if ($query == null){
                return $this->sendError('Not Found.',['error'=>'Dashboard not found']);
            }
            $data = DashboardResource::collection($query->get());
            return $this->sendResponse($data,'Get data Successfully', 200);
        }
        $ids = User::find($user->id)->departments()->allRelatedIds();
        $dashboard = Dashboard::select('dashboards.id','dashboards.name','dashboards.description','dashboards.image','dashboards.url')
            ->leftJoin('department_dashboard','dashboard_id','dashboards.id')
                ->whereIn('department_dashboard.department_id', $ids)
                    ->orWhere('dashboards.permission','=',true)
                    ->where('dashboards.id', '=', $dashboard)
                        ->distinct()
                            ->get();
        if($dashboard == null){
            return $this->sendError('Not Found.',['error'=>'Dashboard not found']);
        }
        $data = DashboardResource::collection($dashboard);
        return $this->sendResponse($data, 'Return Successfully', 200);
    }
    public function update(int $dashboard, DashboardRequest $request,Authenticatable $user){
        if ($user->tokenCan('is_admin')){
            $data = Dashboard::find($dashboard);
            if ($data == null){
                return $this->sendError('Not Found.',['error'=>'Dashboard not found']);
            }
            $data->update($request->all());
            $data->departments()->sync($request->input('departments'));
            return $this->sendResponse($data->fresh(),'Updated Successfully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }
    public function destroy(int $dashboard, Authenticatable $user){
        if ($user->tokenCan('is_admin')){
            $data = Dashboard::find($dashboard);
            if ($data == null){
                return $this->sendError('Not Found.',['error'=>'Dashboard not found']);
            }
            $data->delete();
            return $this->sendResponse(response()->noContent(),'Delete Successfully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }
}
