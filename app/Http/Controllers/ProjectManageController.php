<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectManageController extends Controller
{
    private function ensureOwner(Project $project): void
    {
        abort_unless((int) $project->owner_id === (int) Auth::id(), 403);
    }

    private function normalizeGithubRepo(string $input): string
    {
        $raw = trim($input);
        $raw = rtrim($raw, '/');
        $raw = preg_replace('#^https?://(www\.)?github\.com/#', '', $raw);
        $raw = ltrim($raw, '/');

        return 'https://github.com/' . $raw;
    }

    /**
     * SHOW MANAGE PAGE
     * ✅ No abort here
     */
    public function index(Project $project)
    {
        $isOwner = (int) $project->owner_id === (int) Auth::id();

        return view('projects.sections.manage', compact('project', 'isOwner'));
    }

    /**
     * RENAME PROJECT
     */
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

    /**
     * UPDATE GITHUB REPO
     */
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

    /**
     * REMOVE GITHUB REPO
     */
    public function removeGithub(Project $project)
    {
        $this->ensureOwner($project);

        $project->update([
            'github_repo' => null,
            'github_token' => null,
        ]);

        return back()->with('status', 'github-removed');
    }

    /**
     * DELETE PROJECT
     */
    public function destroy(Project $project)
    {
        $this->ensureOwner($project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('status', 'project-deleted');
    }
}
