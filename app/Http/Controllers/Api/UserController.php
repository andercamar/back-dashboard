<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Authenticatable $user)
    {
        if ($user->tokenCan('is_admin')){
            $data = User::all();
            return $this->sendResponse($data, 'Get data successfully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Authenticatable $user)
    {
        if ($user->tokenCan('is_admin')){
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $data = User::create($input);
            return $this->sendResponse($data, 'Get data successfully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id, Authenticatable $user)
    {
        if ($user->tokenCan('is_admin')){
            $data = User::find($id);
            if ($data == null){
                return $this->sendError('Not Found.',['error'=>'User not found']);
            }
            return $this->sendResponse($data,'Get data Successfully', 200);
        }elseif ($user->tokenCan('is_viewer')) {
            return $this->sendResponse($user,'Get data Successfully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request, Authenticatable $user)
    {
        if($user->tokenCan('is_admin')){
            $data = User::find($id);
            if($data == null){
                return $this->sendError('Not Found.',['error'=>'User not found']);
            }
            $data->update($request->all());
            return $this->sendResponse($data->fresh(), 'Update Successgully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Authenticatable $user)
    {
        if($user->tokenCan('is_admin')){
            $data = User::find($id);
            if ($data == null){
                return $this->sendError('Not Found.',['error'=>'User not found']);
            }
            $data->delete();
            return $this->sendResponse(response()->noContent(),'Delete Successfully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }
    public function departments($id,Request $request, Authenticatable $user){
        if($user->tokenCan('is_admin')){
            $data = User::find($id);
            if ($data == null){
                return $this->sendError('Not Found.',['error'=>'User not found']);
            }
            $data->departments()->sync($request->input('departments'));
            return $this->sendResponse($data->departments()->allRelatedIds()->toArray(),'Update Successfully', 200);
        }
        return $this->sendError('Unauthorized.',['error'=>'Unauthorized']);
    }
}
