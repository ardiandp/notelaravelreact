<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalNotes = Note::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
        $newNotesThisMonth = Note::whereMonth('created_at', now()->month)->count();

        $recentUsers = User::latest()->take(5)->get()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'is_admin' => $u->is_admin,
            'created_at' => $u->created_at->diffForHumans(),
        ]);

        $recentNotes = Note::with('user')->latest()->take(5)->get()->map(fn ($n) => [
            'id' => $n->id,
            'content' => $n->content,
            'user' => $n->user?->name ?? 'Unknown',
            'created_at' => $n->created_at->diffForHumans(),
        ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalNotes' => $totalNotes,
                'newUsersThisMonth' => $newUsersThisMonth,
                'newNotesThisMonth' => $newNotesThisMonth,
            ],
            'recentUsers' => $recentUsers,
            'recentNotes' => $recentNotes,
        ]);
    }
}
