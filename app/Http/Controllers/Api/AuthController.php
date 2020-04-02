<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ValidationService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $validation;
    public function __construct(ValidationService $validation)
    {
        $this->validation = $validation;
    }

    public function register(Request $request)
    {
        $data = $request->all();
        $validator = $this->validation->validateRegister($data);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 417);
        }
        $user = new User([
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        $token = $user->createToken('Personal Access Token')->accessToken;

        return response()->json([
            'message' => 'Successfully created user!',
            'token' => $token
        ], 200);
    }

    public function login(Request $request)
    {
        $data = $request->all();
        $validator = $this->validation->validateLogin($data);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 417);
        }
        $credentials = $request->only(['email', 'password']);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('Personal Access Token')->accessToken;
            return response()->json(['message' => 'Successfully logged in!', 'token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard()->logout();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }


}
