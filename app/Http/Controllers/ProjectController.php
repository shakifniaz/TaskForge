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
            'github_repo' => ['nullable', 'string', 'max:255'],
        ]);

        $github = null;
        if (!empty($data['github_repo'])) {
            $raw = trim($data['github_repo']);
            $raw = rtrim($raw, '/');
            $raw = preg_replace('#^https?://(www\.)?github\.com/#', '', $raw);
            $raw = ltrim($raw, '/');
            $github = 'https://github.com/' . $raw;
        }

        $project = \App\Models\Project::create([
            'name' => $data['name'],
            'owner_id' => auth()->id(),
            'github_repo' => $github,
        ]);

        $project->users()->attach(auth()->id(), ['role' => 'owner']);

        return redirect()->route('projects.overview', $project);
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
