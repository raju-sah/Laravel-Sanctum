<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserLoginRequest;
use App\Http\Requests\Api\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
       $data = $request->validated();
       User::create([
           'name' => $data['name'],
           'email' => $data['email'],
           'password' => bcrypt($data['password']),
       ]);

       return response()->json(['message' => 'User created successfully'], 201);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
           if(!auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
               return response()->json(['message' => 'Unable to login due to invalid credentials'], 400);
           }

          $token = auth()->user()->createToken('authToken')->plainTextToken;
          return response()->json(['access_token' => $token, 'token_type' => 'Bearer'], 200);
    }

    public function profile(): JsonResponse
    {
        return response()->json(['user' => auth()->user()], 200);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout successfully'], 200);
    }
}
