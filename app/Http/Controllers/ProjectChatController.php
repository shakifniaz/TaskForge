<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectChatController extends Controller
{
    private function ensureMember(Project $project): void
    {
        abort_unless(
            $project->users()->where('users.id', Auth::id())->exists(),
            403
        );
    }

    public function index(Project $project)
    {
        $this->ensureMember($project);

        // initial load: last 50 messages
        $messages = ProjectMessage::query()
            ->where('project_id', $project->id)
            ->with('user:id,name,username')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return view('projects.sections.chat', [
            'project' => $project,
            'messages' => $messages,
        ]);
    }

    // JSON feed for polling
    public function feed(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $afterId = (int) $request->query('after_id', 0);

        $query = ProjectMessage::query()
            ->where('project_id', $project->id)
            ->with('user:id,name,username')
            ->orderBy('id');

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        } else {
            // first poll: send last 50
            $query->limit(50);
        }

        $messages = $query->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'body' => $m->body,
                'created_at' => $m->created_at->toDateTimeString(),
                'user' => [
                    'id' => $m->user->id,
                    'name' => $m->user->name,
                    'username' => $m->user->username,
                ],
                'attachment' => $m->attachment_path ? [
                    'url' => asset('storage/' . $m->attachment_path),
                    'name' => $m->attachment_name,
                    'mime' => $m->attachment_mime,
                    'size' => $m->attachment_size,
                ] : null,
            ];
        });


        return response()->json($messages);
    }

    public function store(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:2000'],
            'file' => ['nullable', 'file', 'max:10240'], // 10MB
        ]);

        // Must send either text or a file
        if ((!isset($data['body']) || trim($data['body']) === '') && !$request->hasFile('file')) {
            return response()->json(['ok' => false, 'message' => 'Message or file required'], 422);
        }

        $message = new ProjectMessage();
        $message->project_id = $project->id;
        $message->user_id = Auth::id();
        $message->body = $data['body'] ?? '';

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $path = $file->store("project-chat/{$project->id}", 'public');

            $message->attachment_path = $path;
            $message->attachment_name = $file->getClientOriginalName();
            $message->attachment_mime = $file->getMimeType();
            $message->attachment_size = $file->getSize();
        }

        $message->save();

        return response()->json([
            'ok' => true,
            'id' => $message->id,
        ]);
    }

}
