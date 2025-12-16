<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectMembersController extends Controller
{
    public function index(Project $project)
    {
        abort_unless(
            $project->users()->where('users.id', Auth::id())->exists(),
            403
        );

        $members = $project->users()
            ->select('users.id', 'users.name', 'users.username', 'users.email')
            ->get();

        return view('projects.sections.members', [
            'project' => $project,
            'members' => $members,
        ]);
    }
}
