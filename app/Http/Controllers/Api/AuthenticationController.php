<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\CommonFunctions;
use App\Models\User;
use Validator;
use Auth;

class AuthenticationController extends Controller
{
    
    public function register(Request $request)
    {
        if ($this->ValidateUser($request)->fails()) {
            $jsonArray = ['status' => 'validationError', 'messages' => $this->ValidateUser($request)->messages()];
        } else {
            $user = User::create($request->all());
            $jsonArray = [
                'status' => "success",
                'message' => 'Your registration is successfully completed',
                'token' =>  $user->createToken("API TOKEN")->plainTextToken
            ];
        }
        return response()->json($jsonArray);
    }

    private function ValidateUser($request): object
    {
        return Validator::make($request->all(), [
            'name' => 'bail|required|min:3',
            'email' => 'bail|required|email|unique:users,email',
            'password' => 'bail|required|min:5|max:15'
        ],[
            'name.required' => 'Name is mandatory.',
            'email.required' => 'Email is mandatory.',
            'email.email' => 'Email must be valid.',
            'email.unique' => 'Email already exists.',
            'password.required' => 'Password is mandatory.'
        ]);
    }

    public function authenticate(Request $request)
    {
        $validateData = Validator::make($request->all(),[
            'email' => 'bail|required',
            'password' => 'required'
        ],[
            'email.required' => 'Email is mandatory.',
            'password.required' => 'Password is mandatory.'
        ]);
        if ($validateData->fails()) {
            $jsonArray = ['status' => 'validationError', 'messages' => $validateData->messages()];
        } else {
            if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                $user = Auth::user();
                $jsonArray = [
                    'status' => "success",
                    'message' => 'Successfully Login',
                    'token' =>  $user->createToken("API TOKEN")->plainTextToken
                ];
            } else {
                $jsonArray = ['status' => 'error', 'message' => 'Invalid email or password']; 
            }
        }
        return response()->json($jsonArray);
    }

}
