<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectInviteController extends Controller
{
    private function ensureOwner(Project $project): void
    {
        $isOwner = $project->users()
            ->where('users.id', Auth::id())
            ->wherePivot('role', 'owner')
            ->exists();

        abort_unless($isOwner, 403);
    }

    public function search(Project $project, Request $request)
    {
        abort_unless($project->users()->where('users.id', Auth::id())->exists(), 403);

        $q = trim((string) $request->query('q', ''));
        if ($q === '' || mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $existingIds = $project->users()->pluck('users.id')->all();

        $users = User::query()
            ->whereNotIn('id', $existingIds)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('username', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(8)
            ->get(['id', 'name', 'username', 'email']);

        return response()->json($users);
    }

    public function add(Project $project, Request $request)
    {
        $this->ensureOwner($project);

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        // prevent duplicates
        if ($project->users()->where('users.id', $data['user_id'])->exists()) {
            return back()->with('status', 'already-member');
        }

        $project->users()->attach($data['user_id'], ['role' => 'member']);

        return back()->with('status', 'member-added');
    }
}
