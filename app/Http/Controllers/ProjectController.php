<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $projects = $user->projects()
            ->with('owner')
            ->latest()
            ->get();

        return view('projects.index', [
            'projects' => $projects,
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = Auth::user();

        $project = Project::create([
            'owner_id' => $user->id,
            'name' => $data['name'],
        ]);

        // creator becomes owner in the pivot
        $project->users()->attach($user->id, ['role' => 'owner']);

        return redirect()->route('projects.index')->with('status', 'project-created');
    }

    public function show(Project $project)
    {
        // ensure user is a member
        abort_unless($project->users()->where('users.id', Auth::id())->exists(), 403);

        $members = $project->users()->select('users.id', 'users.name', 'users.username', 'users.email')->get();

        return view('projects.show', [
            'project' => $project,
            'members' => $members,
        ]);
    }
}
