<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Authenticatable $user){
        dd($user->tokenCan('is_admin'));
        return Department::paginate(perPage: 10);
    }

    public function store(DepartmentRequest $request){
        return response()
            ->json(Department::create($request->all()), 201);
    }

    public function show(Department $department){
        return $department;
    }
    public function update(Department $department, DepartmentRequest $request){
        $department->update($request->all());
        return $department->fresh();
    }
    public function destroy(Department $department){
        $department->delete();
        return response()->noContent();
    }
}
