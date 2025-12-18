<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectMembersController extends Controller
{
    private function ensureMember(Project $project): void
    {
        abort_unless(
            $project->users()->where('users.id', Auth::id())->exists(),
            403
        );
    }

    private function ensureOwner(Project $project): void
    {
        // Assumes projects table has owner_id
        abort_unless((int)$project->owner_id === (int)Auth::id(), 403);
    }

    public function index(Project $project)
    {
        $this->ensureMember($project);

        $members = $project->users()
            ->select('users.id', 'users.name', 'users.username', 'users.email', 'users.profile_photo_path', 'users.description')
            ->orderByRaw("CASE WHEN users.id = ? THEN 0 ELSE 1 END", [$project->owner_id])
            ->orderBy('name')
            ->get();

        $isOwner = (int)$project->owner_id === (int)Auth::id();

        return view('projects.sections.members', compact('project', 'members', 'isOwner'));
    }

    public function remove(Project $project, User $user)
    {
        $this->ensureOwner($project);

        // Don't allow owner removal
        abort_if((int)$user->id === (int)$project->owner_id, 422);

        // Only remove if they are currently a member
        $isMember = $project->users()->where('users.id', $user->id)->exists();
        abort_unless($isMember, 404);

        $project->users()->detach($user->id);

        return back()->with('status', 'member-removed');
    }
}
