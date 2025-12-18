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
        $isOwner = ($project->owner_id ?? null) === Auth::id(); // if you have owner_id
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

    public function index(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $uploads = ProjectFile::query()
            ->where('project_id', $project->id)
            ->with(['uploader:id,name,username', 'task:id,title'])
            ->orderByDesc('created_at')
            ->get();

        $tasks = Task::query()
            ->where('project_id', $project->id)
            ->orderByDesc('created_at')
            ->get(['id', 'title']);

        [$owner, $repo] = $this->parseRepo($project->github_repo);
        $githubConnected = ($owner !== '' && $repo !== '');

        return view('projects.sections.files', [
            'project' => $project,
            'uploads' => $uploads,
            'tasks' => $tasks,
            'githubConnected' => $githubConnected,
        ]);
    }

    public function upload(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'], // 10MB
            'task_id' => ['nullable', 'integer'],
        ]);

        if (!empty($data['task_id'])) {
            $ok = Task::where('id', $data['task_id'])
                ->where('project_id', $project->id)
                ->exists();

            if (!$ok) {
                return back()->withErrors(['task_id' => 'Selected task is not in this project.'])->withInput();
            }
        }

        $f = $request->file('file');

        $storedPath = $f->store("project-files/{$project->id}", 'public');

        ProjectFile::create([
            'project_id' => $project->id,
            'task_id' => $data['task_id'] ?? null,
            'uploaded_by' => Auth::id(),
            'original_name' => $f->getClientOriginalName(),
            'path' => $storedPath,
            'mime_type' => $f->getClientMimeType(),
            'size' => $f->getSize() ?? 0,
        ]);

        return redirect()->route('projects.files', $project)->with('status', 'file-uploaded');
    }

    public function download(Project $project, ProjectFile $file)
    {
        $this->ensureMember($project);

        abort_unless($file->project_id === $project->id, 404);

        if (!Storage::disk('public')->exists($file->path)) {
            abort(404, 'File not found on disk.');
        }

        return Storage::disk('public')->download($file->path, $file->original_name);
    }

    public function destroy(Project $project, ProjectFile $file)
    {
        $this->ensureMember($project);

        abort_unless($file->project_id === $project->id, 404);

        abort_unless($this->canDelete($project, $file), 403);

        Storage::disk('public')->delete($file->path);
        $file->delete();

        return redirect()->route('projects.files', $project)->with('status', 'file-deleted');
    }

    public function repoIndex(Project $project, Request $request)
    {
        $this->ensureMember($project);

        [$owner, $repo] = $this->parseRepo($project->github_repo);
        if ($owner === '' || $repo === '') {
            return response()->json([
                'connected' => false,
                'message' => 'GitHub repo not connected for this project.',
                'items' => [],
                'path' => '',
                'branch' => null,
            ], 200);
        }

        $path = trim((string)$request->query('path', ''), '/');
        $branch = (string)$request->query('branch', ($project->github_default_branch ?: 'main'));

        $url = "https://api.github.com/repos/{$owner}/{$repo}/contents";
        if ($path !== '') $url .= "/{$path}";

        $res = Http::withHeaders($this->githubHeaders($project))
            ->timeout(10)
            ->get($url, ['ref' => $branch]);

        if (!$res->ok()) {
            $msg = $res->json('message') ?: ('GitHub error ' . $res->status());
            return response()->json([
                'connected' => true,
                'message' => $msg,
                'items' => [],
                'path' => $path,
                'branch' => $branch,
            ], 200);
        }

        $data = $res->json();

        if (isset($data['type']) && $data['type'] === 'file') {
            return response()->json([
                'connected' => true,
                'message' => null,
                'items' => [],
                'path' => $path,
                'branch' => $branch,
                'file' => true,
                'fileMeta' => [
                    'name' => $data['name'] ?? null,
                    'path' => $data['path'] ?? null,
                    'download_url' => $data['download_url'] ?? null,
                    'html_url' => $data['html_url'] ?? null,
                ],
            ], 200);
        }

        $items = collect($data)->map(function ($i) {
            return [
                'name' => $i['name'] ?? '',
                'path' => $i['path'] ?? '',
                'type' => $i['type'] ?? '',
                'size' => $i['size'] ?? null,
                'download_url' => $i['download_url'] ?? null,
                'html_url' => $i['html_url'] ?? null,
                'sha' => $i['sha'] ?? null,
            ];
        })->values()->all();

        usort($items, function ($a, $b) {
            if ($a['type'] === $b['type']) return strcmp($a['name'], $b['name']);
            return $a['type'] === 'dir' ? -1 : 1;
        });

        return response()->json([
            'connected' => true,
            'message' => null,
            'items' => $items,
            'path' => $path,
            'branch' => $branch,
        ], 200);
    }

    public function repoView(Project $project, Request $request)
    {
        $this->ensureMember($project);

        [$owner, $repo] = $this->parseRepo($project->github_repo);
        if ($owner === '' || $repo === '') {
            return response()->json(['ok' => false, 'message' => 'GitHub repo not connected.'], 200);
        }

        $path = trim((string)$request->query('path', ''), '/');
        if ($path === '') {
            return response()->json(['ok' => false, 'message' => 'Missing path.'], 422);
        }

        $branch = (string)$request->query('branch', ($project->github_default_branch ?: 'main'));

        $url = "https://api.github.com/repos/{$owner}/{$repo}/contents/{$path}";

        $res = Http::withHeaders($this->githubHeaders($project))
            ->timeout(10)
            ->get($url, ['ref' => $branch]);

        if (!$res->ok()) {
            $msg = $res->json('message') ?: ('GitHub error ' . $res->status());
            return response()->json(['ok' => false, 'message' => $msg], 200);
        }

        $data = $res->json();

        if (($data['type'] ?? '') !== 'file') {
            return response()->json(['ok' => false, 'message' => 'Not a file.'], 200);
        }

        $content = $data['content'] ?? null;
        $encoding = $data['encoding'] ?? null;

        if (!$content || $encoding !== 'base64') {
            return response()->json([
                'ok' => true,
                'name' => $data['name'] ?? null,
                'path' => $data['path'] ?? null,
                'text' => null,
                'download_url' => $data['download_url'] ?? null,
                'html_url' => $data['html_url'] ?? null,
                'note' => 'File content not available for preview. Use download.',
            ], 200);
        }

        $decoded = base64_decode(str_replace("\n", "", $content));

        return response()->json([
            'ok' => true,
            'name' => $data['name'] ?? null,
            'path' => $data['path'] ?? null,
            'text' => $decoded,
            'download_url' => $data['download_url'] ?? null,
            'html_url' => $data['html_url'] ?? null,
            'note' => null,
        ], 200);
    }

    public function repoDownload(Project $project, Request $request)
    {
        $this->ensureMember($project);

        [$owner, $repo] = $this->parseRepo($project->github_repo);
        if ($owner === '' || $repo === '') {
            abort(404, 'GitHub repo not connected.');
        }

        $path = trim((string)$request->query('path', ''), '/');
        if ($path === '') {
            abort(422, 'Missing path.');
        }

        $branch = (string)$request->query('branch', ($project->github_default_branch ?: 'main'));

        $url = "https://api.github.com/repos/{$owner}/{$repo}/contents/{$path}";

        $res = Http::withHeaders($this->githubHeaders($project))
            ->timeout(15)
            ->get($url, ['ref' => $branch]);

        if (!$res->ok()) {
            $msg = $res->json('message') ?: ('GitHub error ' . $res->status());
            abort(404, $msg);
        }

        $data = $res->json();
        if (($data['type'] ?? '') !== 'file') {
            abort(404, 'Not a file.');
        }

        $content = $data['content'] ?? null;
        $encoding = $data['encoding'] ?? null;

        $filename = $data['name'] ?? basename($path);
        $mime = $data['mime_type'] ?? 'application/octet-stream';

        if ($content && $encoding === 'base64') {
            $bytes = base64_decode(str_replace("\n", "", $content));

            return response($bytes, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        if (!empty($data['download_url'])) {
            return redirect()->away($data['download_url']);
        }

        abort(404, 'Unable to download this file.');
    }

}
