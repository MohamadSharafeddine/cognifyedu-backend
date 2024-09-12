<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $users = User::all();
            return response()->json($users);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch users'], 500);
        }
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

            $token = JWTAuth::fromUser($user);

            return response()->json(['user' => $user, 'token' => $token], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $data = $request->validated();
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $user->update($data);
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update user'], 500);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();
            return response()->json(['message' => 'Successfully deleted user'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete user'], 500);
        }
    }

    public function login(): JsonResponse
    {
        try {
            $credentials = request(['email', 'password']);
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            return response()->json(['user' => auth()->user(), 'token' => $token]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Login failed'], 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            return response()->json(['message' => 'Successfully logged out']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Logout failed'], 500);
        }
    }

    public function getUserByEmail($email): JsonResponse
    {
        try {
            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            return response()->json(['user_id' => $user->id]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user'], 500);
        }
    }
    
}
