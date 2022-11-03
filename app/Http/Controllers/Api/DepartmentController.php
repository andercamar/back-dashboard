<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\DepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class DepartmentController extends BaseController
{
    public function index(Authenticatable $user){
        if ($user->tokenCan('is_admin')){
            $departments = Department::get();
            $data = DepartmentResource::collection($departments);
            return $this->sendResponse($data, 'Get data successfully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }

    public function store(DepartmentRequest $request, Authenticatable $user){
        if ($user->tokenCan('is_admin')){
            $data = Department::create($request->all());
            return $this->sendResponse($data, 'Create Successfully', 201);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }

    public function show(int $department, Authenticatable $user){
        if ($user->tokenCan('is_admin')){
            $query = Department::find($department);
            if ($query == null){
                return $this->sendError('Not Found.',['error'=>'Department not found']);
            }
            $data = DepartmentResource::collection($query);
            return $this->sendResponse($data,'Get data Successfully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }
    public function update(int $department, DepartmentRequest $request, Authenticatable $user){
        if ($user->tokenCan('is_admin')){
            $data = Department::find($department);
            if ($data == null){
                return $this->sendError('Not Found.',['error'=>'Department not found']);
            }
            $data->update($request->all());
            return $this->sendResponse($data->fresh(),'Get data Successfully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }
    public function destroy(int $department, Authenticatable $user){
        if($user->tokenCan('is_admin')){
            $data = Department::find($department);
            if ($data == null){
                return $this->sendError('Not Found.',['error'=>'Department not found']);
            }
            $data->delete();
            return $this->sendResponse(response()->noContent(),'Delete Successfully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }
}
