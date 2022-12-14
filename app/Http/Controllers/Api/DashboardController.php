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
        $dashboards = Dashboard::select("dashboards.id","dashboards.name","dashboards.description","dashboards.image","dashboards.creator","dashboards.status")
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
            $input = $request->except(['image']);
            $data = Dashboard::create($input);
            $data->departments()->attach($request->input('departments'));
            if($request->hasFile('image')){
                $image = $request->file('image');
                $extension = $image->getClientOriginalExtension();
                $name = "{$data->id}-{$data->name}.{$extension}";
                $image->move(public_path('image'),$name);
                $data->image = $name;
                $data->save();
            }
            return $this->sendResponse($data, 'Create Successfully', 201);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }

    public function show(int $dashboard,Authenticatable $user){
        if ($user->tokenCan('is_admin')){
            $data = Dashboard::find($dashboard);
            if ($data == null){
                return $this->sendError('Not Found.',['error'=>'Dashboard not found']);
            }
            return $this->sendResponse($data,'Get data Successfully', 200);
        }
        $ids = User::find($user->id)->departments()->allRelatedIds();
        $data = Dashboard::select('dashboards.id','dashboards.name','dashboards.description','dashboards.image','dashboards.url','dashboards.creator','dashboards.status')
            ->leftJoin('department_dashboard','dashboard_id','dashboards.id')
                ->where(function ($query) use ($dashboard){
                    $query->where('dashboards.id','=',$dashboard);
                })
                ->where(function ($query) use ($ids){
                    $query->whereIn('department_dashboard.department_id',$ids);
                    $query->orWhere('dashboards.permission','=',true);
                })->get();
        if($data == null){
            return $this->sendError('Not Found.',['error'=>'Dashboard not found']);
        }
        return $this->sendResponse($data[0], 'Return Successfully', 200);
    }
    public function update(int $dashboard, DashboardRequest $request,Authenticatable $user){
        if ($user->tokenCan('is_admin')){
            $data = Dashboard::find($dashboard);
            if ($data == null){
                return $this->sendError('Not Found.',['error'=>'Dashboard not found']);
            }
            $data->update($request->except(['image','departments']));
            if($request->hasFile('image')){
                $image = $request->file('image');
                $extension = $image->getClientOriginalExtension();
                $name = "{$data->id}-{$data->name}.{$extension}";
                $image->move(public_path('image'),$name);
                $data->image = $name;
                $data->save();
            }
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
