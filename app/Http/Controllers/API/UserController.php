<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('department');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        return ResponseFormatter::success($query->paginate($request->input('limit', 10)), 'Get data user successfully');
    }

    public function show(string $id)
    {
        $user = User::with('department')->find($id);

        if (!$user) {
            return ResponseFormatter::error('User not found', 404);
        }

        return ResponseFormatter::success($user, 'User found');
    }

    public function store(CreateUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'department_id' => $request->department_id,
                'role'          => $request->role ?? User::ROLE_EMPLOYEE,
                'phone'         => $request->phone,
                'is_active'     => $request->is_active,
            ]);
            return ResponseFormatter::success($user->load('department'), 'User created successfully', 201);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return ResponseFormatter::error('User not found', 404);
            }

            if ($request->user()->id === $id) {
                return ResponseFormatter::error("Can't update own account", 403);
            }

            $data = $request->only(['name', 'email', 'department_id', 'role', 'phone', 'is_active']);

            if ($request->hasFile('photo')) {
                if ($user->photo) {

                    Storage::disk('public')->delete($user->getRawOriginal('photo'));
                }
                $photo = $request->file('photo')->store('users', 'public');
                $data['photo'] = $photo;
            }

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return ResponseFormatter::success($user->fresh()->load('department'), 'User updated successfully');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function updateRole(Request $request, string $id)
    {
        try {
            $request->validate(['role' => 'required|in:' . implode(',', User::ROLES)]);

            $user = User::find($id);

            if (!$user) {
                return ResponseFormatter::error('User not found', 404);
            }

            if ($request->user()->id === $id) {
                return ResponseFormatter::error("Can't update own role account", 404);
            }

            $user->update(['role' => $request->role]);

            return ResponseFormatter::success($user->fresh()->load('department'), 'Role user updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return ResponseFormatter::error('User not found', 404);
            }

            if ($request->user()->id === $id) {
                return ResponseFormatter::error("Can't update own role account", 404);
            }

            $user->tokens()->delete();
            $user->delete();

            return ResponseFormatter::success(null, 'User deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
