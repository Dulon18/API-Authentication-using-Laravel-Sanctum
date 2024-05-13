<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
    {
    public function register(Request $request)
    {
        $registerUserData = $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:8'
        ]);
        $user = User::create([
            'name' => $registerUserData['name'],
            'email' => $registerUserData['email'],
            'password' => Hash::make($registerUserData['password']),
        ]);
        return response()->json([
            'message' => 'User Created ',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            ]);
        
            $credentials = request(['email','password']);
            if(!Auth::attempt($credentials))
            {
            return response()->json([
                'message' => 'Invalid credential or Unauthorized'
            ],401);
            }
        
            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;
        
            return response()->json([
            'accessToken' =>$token,
            'token_type' => 'Bearer',
            ]);
    }
    public function logout()
    {
    $user = Auth::user();
    $user->tokens()->delete();

    return response()->json([
        "message" => "Logged out"
    ]);
    }

    public function userList()
    {
        $user = DB::table("users")->select('*')->get();
        return response()->json($user);

    }
}
