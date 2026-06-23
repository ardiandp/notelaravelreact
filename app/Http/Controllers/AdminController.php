<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data = [
            'totalUsers' => User::count(),
            'totalNotes' => Note::count(),
            'newUsers' => User::whereMonth('created_at', now()->month)->count(),
            'newNotes' => Note::whereMonth('created_at', now()->month)->count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentNotes' => Note::with('user')->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', $data);
    }

    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole(Role::findById($validated['role']));

        return redirect()->route('admin.users')->with('success', 'User created.');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'role' => 'required|exists:roles,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([Role::findById($validated['role'])]);

        return redirect()->route('admin.users')->with('success', 'User updated.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'Cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted.');
    }
}
