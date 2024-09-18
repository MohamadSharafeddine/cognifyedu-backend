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
use Illuminate\Support\Facades\Storage;

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
            
            if (!isset($data['profile_picture'])) {
                $data['profile_picture'] = 'public/profile_pictures/default.jpg';
            }
            
            $user = User::create($data);
            
            $token = JWTAuth::fromUser($user);
    
            if ($user->profile_picture) {
                $user->profile_picture = Storage::url($user->profile_picture);
            }
    
            return response()->json(['user' => $user, 'token' => $token], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }
    
    public function show(User $user): JsonResponse
    {
        if ($user->profile_picture) {
            $user->profile_picture = Storage::url($user->profile_picture);
        }
        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $data = $request->validated();
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
    
            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('public/profile_pictures');
                $data['profile_picture'] = $path;
            }
    
            $user->update($data);
    
            if ($user->profile_picture) {
                $user->profile_picture = Storage::url($user->profile_picture);
            }
    
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
            $user = auth()->user();
            
            if ($user->profile_picture) {
                $user->profile_picture = Storage::url($user->profile_picture);
            }
            
            return response()->json(['user' => $user, 'token' => $token]);
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

    public function downloadProfilePicture(User $user)
    {
        try {
            if ($user->profile_picture) {
                $filePath = $user->profile_picture;
                if (Storage::exists($filePath)) {
                    return response()->download(storage_path("app/{$filePath}"));
                }
            }
            return response()->json(['message' => 'Profile picture not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve profile picture'], 500);
        }
    }

    public function addParentToStudent($studentId): JsonResponse
    {
        try {
            $student = User::findOrFail($studentId);
            $parentId = request('parent_id');
    
            if (!$parentId || !User::where('id', $parentId)->where('type', 'parent')->exists()) {
                return response()->json(['message' => 'Parent ID not found or is invalid'], 404);
            }
    
            $student->parent_id = $parentId;
            $student->save();
    
            return response()->json(['message' => 'Parent added successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to add parent to student'], 500);
        }
    }
    
    


public function getChildren($parentId): JsonResponse
{
    try {
        $parent = User::findOrFail($parentId);

        if ($parent->type !== 'parent') {
            return response()->json(['message' => 'User is not a parent'], 400);
        }

        $children = User::where('parent_id', $parentId)->get();

        return response()->json($children);
    } catch (Exception $e) {
        return response()->json(['message' => 'Failed to retrieve children'], 500);
    }
}


}
