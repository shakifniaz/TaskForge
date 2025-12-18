<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectManageController extends Controller
{
    private function ensureOwner(Project $project): void
    {
        abort_unless((int)$project->owner_id === (int)Auth::id(), 403);
    }

    private function normalizeGithubRepo(string $input): string
    {
        $raw = trim($input);

        // Remove trailing slash
        $raw = rtrim($raw, '/');

        // If full URL -> strip domain
        $raw = preg_replace('#^https?://(www\.)?github\.com/#', '', $raw);

        // Ensure it's "username/repo"
        $raw = ltrim($raw, '/');

        return 'https://github.com/' . $raw;
    }

    public function index(Project $project)
    {
        $this->ensureOwner($project);

        return view('projects.sections.manage', compact('project'));
    }

    public function rename(Project $project, Request $request)
    {
        $this->ensureOwner($project);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $project->update([
            'name' => $data['name'],
        ]);

        return back()->with('status', 'project-renamed');
    }

    public function updateGithub(Project $project, Request $request)
    {
        $this->ensureOwner($project);

        $data = $request->validate([
            'github_repo' => ['required', 'string', 'max:255'],
        ]);

        $project->update([
            'github_repo' => $this->normalizeGithubRepo($data['github_repo']),
        ]);

        return back()->with('status', 'github-updated');
    }

    public function removeGithub(Project $project)
    {
        $this->ensureOwner($project);

        $project->update([
            'github_repo' => null,
            'github_token' => null, // optional: only if you use token
        ]);

        return back()->with('status', 'github-removed');
    }

    public function destroy(Project $project)
    {
        $this->ensureOwner($project);

        $project->delete();

        return redirect()->route('projects.index')->with('status', 'project-deleted');
    }
}
