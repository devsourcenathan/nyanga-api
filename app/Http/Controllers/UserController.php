<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'telephone' => 'nullable|string|max:20|unique:users',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'otp' => 'nullable|string|max:6',
            'reset_password_otp' => 'nullable|string|max:6',
            'reset_password_expires' => 'nullable|date',
            'role' => 'required|in:ADMIN,PROVIDER,CLIENT',
            'status' => 'boolean',
        ]);

        $user = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'surname' => $request->surname,
            'password' => bcrypt($request->password),
            'telephone' => $request->telephone,
            'address' => $request->address,
            'logo' => $request->logo,
            'description' => $request->description,
            'otp' => $request->otp,
            'reset_password_otp' => $request->reset_password_otp,
            'reset_password_expires' => $request->reset_password_expires,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        return $user;
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
            'telephone' => 'nullable|string|max:20|unique:users,telephone,' . $user->id,
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'otp' => 'nullable|string|max:6',
            'reset_password_otp' => 'nullable|string|max:6',
            'reset_password_expires' => 'nullable|date',
            'role' => 'required|in:ADMIN,PROVIDER,CLIENT',
            'status' => 'boolean',
        ]);

        $user->update($request->all());

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }

    public function getUsers()
    {
        return User::select('id', 'name', 'email', 'surname', 'telephone', 'role')->get();
    }

    // Get all clients
    public function getClients()
    {
        return User::where('role', 'CLIENT')->select('id', 'name', 'email', 'surname', 'telephone', 'role')->get();
    }

    // Get all providers
    public function getProviders()
    {
        return User::where('role', 'PROVIDER')->select('id', 'name', 'email', 'surname', 'telephone', 'role')->get();
    }

    // Get all admins
    public function getAdmins()
    {
        return User::where('role', 'ADMIN')->select('id', 'name', 'email', 'surname', 'telephone', 'role')->get();
    }
}
