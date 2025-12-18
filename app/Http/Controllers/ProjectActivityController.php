<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ProjectActivityController extends Controller
{
    private function ensureMember(Project $project): void
    {
        abort_unless(
            $project->users()->where('users.id', Auth::id())->exists(),
            403
        );
    }

    private function githubHeaders(Project $project): array
    {
        $headers = [
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            'User-Agent' => 'TaskForge',
        ];

        if (!empty($project->github_token)) {
            $headers['Authorization'] = 'Bearer ' . $project->github_token;
        }

        return $headers;
    }

    /**
     * Accepts:
     * - owner/repo
     * - https://github.com/owner/repo
     * - https://github.com/owner/repo/
     */
    private function parseRepo(?string $repo): array
    {
        $repo = is_string($repo) ? trim($repo) : '';

        if ($repo === '') {
            abort(422, 'Repository is required.');
        }

        $repo = preg_replace('#^https?://github\.com/#', '', $repo);
        $repo = trim($repo, '/');

        // remove extra segments like /tree/main if pasted
        // owner/repo/tree/main -> owner/repo
        $parts = explode('/', $repo);
        if (count($parts) >= 2) {
            $owner = trim($parts[0] ?? '');
            $name  = trim($parts[1] ?? '');
        } else {
            abort(422, 'Repository must be in owner/repo format.');
        }

        if ($owner === '' || $name === '') {
            abort(422, 'Repository must be in owner/repo format.');
        }

        return [$owner, $name];
    }

    public function index(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $branches = [];
        $commits = [];
        $error = null;

        if ($project->github_repo) {
            try {
                $branches = $this->fetchBranches($project);

                $selected = $request->get('branch')
                    ?: ($project->github_default_branch ?: ($branches[0]['name'] ?? null));

                if ($selected) {
                    $commits = $this->fetchCommits($project, $selected);
                }
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        return view('projects.sections.activity', [
            'project' => $project,
            'branches' => $branches,
            'commits' => $commits,
            'selectedBranch' => $request->get('branch') ?: ($project->github_default_branch ?: null),
            'error' => $error,
        ]);
    }

    public function connect(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $data = $request->validate([
            'github_repo' => ['nullable', 'string', 'max:255'],
            'github_token' => ['nullable', 'string', 'max:500'],
            'github_default_branch' => ['nullable', 'string', 'max:255'],
        ]);

        // Allow disconnect
        if (empty($data['github_repo'])) {
            $project->forceFill([
                'github_repo' => null,
                'github_token' => null,
                'github_default_branch' => null,
            ])->save();

            return redirect()->route('projects.activity', $project)->with('status', 'github-disconnected');
        }

        // Normalize repo format (accept URL or owner/repo)
        [$owner, $name] = $this->parseRepo($data['github_repo']);
        $repoNormalized = $owner . '/' . $name;

        // Save (forceFill avoids fillable issues)
        $project->forceFill([
            'github_repo' => $repoNormalized,
            'github_token' => $data['github_token'] ?: null,
            'github_default_branch' => $data['github_default_branch'] ?: null,
        ])->save();

        // Refresh model so github_repo is definitely loaded
        $project->refresh();

        // Validate repo by calling GitHub
        try {
            $repoInfo = $this->fetchRepoInfo($project);

            // If no default branch saved, use GitHub’s
            if (empty($project->github_default_branch) && !empty($repoInfo['default_branch'])) {
                $project->forceFill(['github_default_branch' => $repoInfo['default_branch']])->save();
            }
        } catch (\Throwable $e) {
            // rollback if invalid
            $project->forceFill([
                'github_repo' => null,
                'github_token' => null,
                'github_default_branch' => null,
            ])->save();

            return back()->withErrors(['github_repo' => 'Could not connect to repo: ' . $e->getMessage()]);
        }

        return redirect()->route('projects.activity', $project)->with('status', 'github-connected');
    }

    // AJAX: branches
    public function branches(Project $project)
    {
        $this->ensureMember($project);

        if (!$project->github_repo) {
            return response()->json([]);
        }

        return response()->json($this->fetchBranches($project));
    }

    // AJAX: commits
    public function commits(Project $project, Request $request)
    {
        $this->ensureMember($project);

        if (!$project->github_repo) {
            return response()->json([]);
        }

        $branch = $request->string('branch')->toString();
        if ($branch === '') {
            return response()->json([], 422);
        }

        return response()->json($this->fetchCommits($project, $branch));
    }

    private function fetchRepoInfo(Project $project): array
    {
        if (!$project->github_repo) {
            throw new \RuntimeException('GitHub repo is not set.');
        }

        [$owner, $name] = $this->parseRepo($project->github_repo);

        $res = Http::withHeaders($this->githubHeaders($project))
            ->timeout(10)
            ->get("https://api.github.com/repos/{$owner}/{$name}");

        if (!$res->ok()) {
            $msg = $res->json('message') ?: ('GitHub error ' . $res->status());
            throw new \RuntimeException($msg);
        }

        return $res->json();
    }

    private function fetchBranches(Project $project): array
    {
        if (!$project->github_repo) {
            return [];
        }

        [$owner, $name] = $this->parseRepo($project->github_repo);

        $res = Http::withHeaders($this->githubHeaders($project))
            ->timeout(10)
            ->get("https://api.github.com/repos/{$owner}/{$name}/branches", [
                'per_page' => 100,
            ]);

        if (!$res->ok()) {
            $msg = $res->json('message') ?: ('GitHub error ' . $res->status());
            throw new \RuntimeException($msg);
        }

        $data = $res->json() ?? [];

        return collect($data)->map(function ($b) {
            return [
                'name' => $b['name'] ?? '',
                'sha' => $b['commit']['sha'] ?? null,
            ];
        })->filter(fn ($b) => $b['name'] !== '')->values()->all();
    }

    private function fetchCommits(Project $project, string $branch): array
    {
        if (!$project->github_repo) {
            return [];
        }

        [$owner, $name] = $this->parseRepo($project->github_repo);

        $res = Http::withHeaders($this->githubHeaders($project))
            ->timeout(10)
            ->get("https://api.github.com/repos/{$owner}/{$name}/commits", [
                'sha' => $branch,
                'per_page' => 30,
            ]);

        if (!$res->ok()) {
            $msg = $res->json('message') ?: ('GitHub error ' . $res->status());
            throw new \RuntimeException($msg);
        }

        $data = $res->json() ?? [];

        return collect($data)->map(function ($c) {
            return [
                'sha' => substr($c['sha'] ?? '', 0, 7),
                'full_sha' => $c['sha'] ?? '',
                'message' => $c['commit']['message'] ?? '',
                'author' => $c['commit']['author']['name'] ?? ($c['author']['login'] ?? 'Unknown'),
                'date' => $c['commit']['author']['date'] ?? null,
                'url' => $c['html_url'] ?? null,
            ];
        })->all();
    }
}
