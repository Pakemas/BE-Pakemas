<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    public function register(Request $request){
		$validator = Validator::make($request->all(),[
			'name' => 'required',
			'email' => 'required',
			'password' => 'required',
			'c_password' => 'required|same:password',
	]);
	
		if($validator->fails()){
			return $this->sendError('Validation Error.', $validator->errors(),422);
		}
		$input = $request->all();
		$input['password'] = bcrypt($input['password']);
		$user = User::create($input);

		$success['user'] = $user;
		
		return $this->sendResponse($success, 'User register successfully.');
	}

    public function login(Request $request){
		$credentials = $request->only(['email', 'password']);
		
		if(! $token = Auth::attempt($credentials)){
			return $this->sendError('Unathorized.', ['error' => 'Unathorized'], 401);
		}
		
		$success = $this->respondWithToken($token);
		
		return $this->sendResponse($success, 'User login successfuly.');
	}
    public function profile(){
		$success = Auth::user();
		
		return $this->sendResponse($success, 'profile fetch successfuly.');
	}

    public function refresh(){
		$success = $this->respondWithToken(Auth::refresh());
		
		return $this->sendResponse($success, 'Refresh bearer successfuly.');

	}

    public function logout(){
		$success = Auth::logout();
		
		return $this->sendResponse($success, 'Successfuly logged out.');
	}



    protected function respondWithToken($token){
		return [
			'access_token' => $token,
			'token_type' => 'bearer',
			'expires_in' => Auth::factory()->getTTL() * 60,
		];
	}

    
}
