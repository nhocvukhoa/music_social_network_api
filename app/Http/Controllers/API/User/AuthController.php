<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\LogoutRequest;

class AuthController extends Controller
{
    /**
     * Register an account
     *
     * @param \Illuminate\Http\Request  $request
     * @return void
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password'))
            ]);
    
            $token = $user->createToken('user_token')->plainTextToken;
    
            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => __('auth.register.success')
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong'
            ]);
        }
    }

    /**
     * Login account
     *
     * @param \Illuminate\Http\Request  $request
     * @return void
     */
    public function login(LoginRequest $request) 
    {
        try {
            $user = User::where('email', '=', $request->input('email'))->firstOrFail();

            if (Hash::check($request->input('password'), $user->password)) {
                $token = $user->createToken('user_token')->plainTextToken;

                return response()->json([
                    'user' => $user,
                    'token' => $token,
                    'message' => __('auth.login.success')
                ], 200);
            }

            return response()->json([
                'error' => __('auth.login.error')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'token' => 'Something went wrong'
            ]);
        }
    }

     /**
     * Logout account
     *
     * @param \Illuminate\Http\Request  $request
     * @return void
     */
    public function logout(LogoutRequest $request) 
    {
        try {
            $user = User::findOrFail($request->input('user_id'));

            $user->tokens()->delete();

            return response()->json([
                'user' => $user,
                'message' => 'User logged out'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'token' => 'Something went wrong'
            ]);
        }
    }
}
