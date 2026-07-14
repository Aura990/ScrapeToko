<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        $users = User::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
            })
            ->get()
            ->sortBy(function ($user) {
                $is_online = $user->last_seen && Carbon::parse($user->last_seen)->diffInMinutes(now()) <= 2;
                return [$is_online ? 0 : 1, $user->name];
            });
    
        return view('users.index', compact('users'));
    }    

    public function statuses()
    {
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_online' => $user->last_seen && \Carbon\Carbon::parse($user->last_seen)->diffInMinutes(now()) <= 2,
            ];
        })->sort(function ($a, $b) {
            if ($a['is_online'] === $b['is_online']) {
                return strcmp($a['name'], $b['name']);
            }
    
            return $a['is_online'] ? -1 : 1;
        })->values()->all();
    
        return response()->json($users);
    }    

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
        ]);
    
        $name = ucwords(strtolower($request->name));
    
        User::create([
            'name' => $name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);
    
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string',
        ]);
    
        $name = ucwords(strtolower($request->name));
    
        $user->update([
            'name' => $name,
            'email' => $request->email,
            'role' => $request->role,
        ]);
    
        if ($request->filled('password')) {
            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }
    
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }    

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
    
}
