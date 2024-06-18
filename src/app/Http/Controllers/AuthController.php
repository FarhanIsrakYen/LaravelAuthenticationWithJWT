<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(CreateUserRequest $createUserRequest) : JsonResponse {
        $user = User::create([
            'name' => $createUserRequest->name,
            'email' => $createUserRequest->email,
            'username' => $createUserRequest->username,
            'password' => $createUserRequest->password,
            'phone' => $createUserRequest->phone,
            'address' => $createUserRequest->address,
        ]);
        return response()->json([
            'status' => Response::HTTP_ACCEPTED,
            'message' => "Signed up successfully",
            'user' => $user
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Get a JWT token via given credentials.
     * @param LoginUserRequest $loginUserRequest
     * @return JsonResponse
     */
    public function login(LoginUserRequest $loginUserRequest): JsonResponse
    {
        $user = User::firstWhere('email', $loginUserRequest->email);
        if (empty($user)) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => "No email found. Please sign up first to continue",
                'user' => $loginUserRequest->email
            ], Response::HTTP_NOT_FOUND);
        }
        if (!$user->is_active) {
            return response()->json([
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => "Account was deactivated! Please request for account reactivation",
                'user' => $loginUserRequest->email
            ], Response::HTTP_UNAUTHORIZED);
        }

        $credentials = $loginUserRequest->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Authentication failed! Please try again!',
                'data' => []
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'access_token' => $token,
            'status' => Response::HTTP_OK,
            'message' => 'Logged in successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }
}
