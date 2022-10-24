<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class RegisterController extends BaseController
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email',
            'password'  => 'required',
            'c_password'=> 'required|same:password',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('DashApp', ['is_viewer'])->plainTextToken;
        $success['name'] = $user->name;
        return $this->sendResponse($success, 'User register successfully', 200);
    }
    public function login(Request $request){
        if(Auth::attempt(['email' => $request->email, 'password' =>  $request->password])){
            $user = Auth::user();
            if($user->is_admin){
                $success['token']   = $user->createToken('DashApp', ['is_admin'])->plainTextToken;
            }else{
                $success['token']   = $user->createToken('DashApp', ['is_viewer'])->plainTextToken;
            }
            $success['name']    = $user->name;
            return $this->sendResponse($success, 'User Login Successfully.', 200);
        }else{
            return $this->sendError('unauthorized.',['error'=>'unauthorized']);
        }
    }
}
