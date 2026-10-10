<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('department')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'department_id' => 'required|exists:departments,id',
            'role'          => 'required|in:employee,manager,purchasing,warehouse,admin',
            'phone'         => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'department_id' => $request->department_id,
            'role'          => $request->role,
            'phone'         => $request->phone,
            'is_active'     => true,
        ]);

        return redirect()->route('web.users.index')
            ->with('success', "Akun pengguna '{$user->name}' berhasil dibuat dengan peran " . strtoupper($user->role));
    }

    public function updateRole(string $id, Request $request)
    {
        $request->validate([
            'role' => 'required|in:employee,manager,purchasing,warehouse,admin',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'role' => $request->role,
        ]);

        return back()->with('success', "Peran pengguna '{$user->name}' berhasil diubah menjadi " . strtoupper($user->role));
    }
}
