<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{

    /**
     * Get the authenticated User
     * @return JsonResponse
     */
    public function getUser(): JsonResponse
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Deactivate the authenticated User
     * @return JsonResponse
     */
    public function deactivateUser(): JsonResponse
    {
        $user = $this->guard()->user();
        $user->update([
            'is_active' => false
        ]);

        return response()->json([
            'status' => Response::HTTP_ACCEPTED,
            'message' => "Account deactivated successfully",
            'user' => $user
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Update the authenticated User
     */
    public function updateUser(UpdateUserRequest $updateUserRequest): JsonResponse
    {
        $user = $this->guard()->user();
        $user->update([
            'name' => empty($updateUserRequest->name)? $user->name : $updateUserRequest->name,
            'email' => empty($updateUserRequest->email)? $user->email : $updateUserRequest->email,
            'password' => empty($updateUserRequest->password)? $user->password : $updateUserRequest->password,
            'phone' => empty($updateUserRequest->phone)? $user->phone : $updateUserRequest->phone,
            'address' => empty($updateUserRequest->address)? $user->address : $updateUserRequest->address,
        ]);
        return response()->json([
            'status' => Response::HTTP_ACCEPTED,
            'message' => "User updated successfully",
            'user' => $user
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Deactivate user account
     */
    public function updatePassword(UpdatePasswordRequest $updatePasswordRequest): JsonResponse
    {
        $user = $this->guard()->user();

        $user->update([
            'password' => Hash::make($updatePasswordRequest->confirmPassword),
        ]);
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'status' => Response::HTTP_ACCEPTED,
            'message' => "Password updated successfully. Please login again to continue",
            'user' => $user
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            // Invalidate the token
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout, please try again'
            ], 500);
        }
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken($this->guard()->refresh());
    }
}
