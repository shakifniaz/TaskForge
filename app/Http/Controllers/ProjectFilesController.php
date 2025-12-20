<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProjectFilesController extends Controller
{
    private function ensureMember(Project $project): void
    {
        abort_unless(
            $project->users()->where('users.id', Auth::id())->exists(),
            403
        );
    }

    private function canDelete(Project $project, ProjectFile $file): bool
    {
        $isOwner = ($project->owner_id ?? null) === Auth::id();
        $isUploader = $file->uploaded_by === Auth::id();

        return $isOwner || $isUploader;
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

    private function parseRepo(?string $repo): array
    {
        $repo = is_string($repo) ? trim($repo) : '';
        if ($repo === '') return ['', ''];

        $repo = preg_replace('#^https?://github\.com/#', '', $repo);
        $repo = trim($repo, '/');

        $parts = explode('/', $repo);
        if (count($parts) < 2) return ['', ''];

        return [trim($parts[0]), trim($parts[1])];
    }

    /* =========================
       INDEX
    ========================== */
    public function index(Project $project)
    {
        $this->ensureMember($project);

        $uploads = ProjectFile::where('project_id', $project->id)
            ->with(['uploader:id,name,username', 'task:id,title'])
            ->latest()
            ->get();

        $tasks = Task::where('project_id', $project->id)
            ->latest()
            ->get(['id', 'title']);

        [$owner, $repo] = $this->parseRepo($project->github_repo);
        $githubConnected = ($owner !== '' && $repo !== '');

        return view('projects.sections.files', compact(
            'project',
            'uploads',
            'tasks',
            'githubConnected'
        ));
    }

    /* =========================
       UPLOAD
    ========================== */
    public function upload(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'task_id' => ['nullable', 'integer'],
        ]);

        if (!empty($data['task_id'])) {
            abort_unless(
                Task::where('id', $data['task_id'])
                    ->where('project_id', $project->id)
                    ->exists(),
                422
            );
        }

        $file = $request->file('file');

        $path = $file->store("project-files/{$project->id}", 'public');

        ProjectFile::create([
            'project_id' => $project->id,
            'task_id' => $data['task_id'] ?? null,
            'uploaded_by' => Auth::id(),
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return back()->with('status', 'file-uploaded');
    }

    /* =========================
       DOWNLOAD
    ========================== */
    public function download(Project $project, ProjectFile $file)
    {
        $this->ensureMember($project);
        abort_unless($file->project_id === $project->id, 404);

        abort_unless(
            Storage::disk('public')->exists($file->path),
            404
        );

        return Storage::disk('public')->download(
            $file->path,
            $file->original_name
        );
    }

    /* =========================
       DELETE (FIXED)
    ========================== */
    public function destroy(Project $project, ProjectFile $file)
    {
        $this->ensureMember($project);

        // Ensure file belongs to this project
        abort_unless($file->project_id === $project->id, 404);

        // Permission check (owner or uploader)
        abort_unless($this->canDelete($project, $file), 403);

        // Delete file from storage
        if ($file->path && Storage::disk('public')->exists($file->path)) {
            Storage::disk('public')->delete($file->path);
        }

        // Delete DB record
        $file->delete();

        return redirect()
            ->route('projects.files', $project)
            ->with('status', 'file-deleted');
    }

    public function repoView(Project $project, Request $request)
    {
        $this->ensureMember($project);

        [$owner, $repo] = $this->parseRepo($project->github_repo);
        abort_if($owner === '' || $repo === '', 404);

        $path = trim($request->query('path', ''), '/');
        abort_if($path === '', 422);

        $branch = $project->github_default_branch ?: 'main';

        $url = "https://api.github.com/repos/{$owner}/{$repo}/contents/{$path}";

        $res = Http::withHeaders($this->githubHeaders($project))
            ->get($url, ['ref' => $branch]);

        if (!$res->ok()) {
            return response()->json([
                'text' => null,
                'note' => $res->json('message') ?? 'Unable to load file.',
            ]);
        }

        $data = $res->json();

        if (($data['type'] ?? '') !== 'file') {
            return response()->json([
                'text' => null,
                'note' => 'Not a file.',
            ]);
        }

        // If GitHub didn't return text preview
        if (($data['encoding'] ?? '') !== 'base64' || empty($data['content'])) {
            return response()->json([
                'text' => null,
                'note' => 'Preview not available for this file type.',
            ]);
        }

        return response()->json([
            'text' => base64_decode(str_replace("\n", '', $data['content'])),
            'note' => null,
        ]);
    }

    public function repoDownload(Project $project, Request $request)
    {
        $this->ensureMember($project);

        [$owner, $repo] = $this->parseRepo($project->github_repo);
        abort_if($owner === '' || $repo === '', 404);

        $path = trim($request->query('path', ''), '/');
        abort_if($path === '', 422);

        $branch = $project->github_default_branch ?: 'main';

        $url = "https://api.github.com/repos/{$owner}/{$repo}/contents/{$path}";

        $res = Http::withHeaders($this->githubHeaders($project))
            ->get($url, ['ref' => $branch]);

        abort_if(!$res->ok(), 404);

        $data = $res->json();
        abort_if(($data['type'] ?? '') !== 'file', 404);

        $filename = $data['name'] ?? basename($path);

        if (($data['encoding'] ?? '') === 'base64' && !empty($data['content'])) {
            $bytes = base64_decode(str_replace("\n", '', $data['content']));

            return response($bytes, 200, [
                'Content-Type' => $data['mime_type'] ?? 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        }

        // fallback to GitHub direct download
        if (!empty($data['download_url'])) {
            return redirect()->away($data['download_url']);
        }

        abort(404);
    }


    /* =========================
       REPO BROWSER (UNCHANGED)
    ========================== */
    public function repoIndex(Project $project, Request $request)
    {
        $this->ensureMember($project);

        [$owner, $repo] = $this->parseRepo($project->github_repo);
        if ($owner === '' || $repo === '') {
            return response()->json([
                'connected' => false,
                'message' => 'GitHub repo not connected.',
                'items' => [],
            ]);
        }

        $path = trim((string)$request->query('path', ''), '/');
        $branch = $project->github_default_branch ?: 'main';

        $url = "https://api.github.com/repos/{$owner}/{$repo}/contents";
        if ($path !== '') $url .= "/{$path}";

        $res = Http::withHeaders($this->githubHeaders($project))
            ->get($url, ['ref' => $branch]);

        if (!$res->ok()) {
            return response()->json([
                'connected' => true,
                'message' => $res->json('message'),
                'items' => [],
            ]);
        }

        $items = collect($res->json())->map(fn ($i) => [
            'name' => $i['name'],
            'path' => $i['path'],
            'type' => $i['type'],
            'download_url' => $i['download_url'] ?? null,
        ])->values();

        return response()->json([
            'connected' => true,
            'items' => $items,
        ]);
    }
}
