<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectBoardController extends Controller
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

        $tasks = Task::query()
            ->where('project_id', $project->id)
            ->with(['assignee:id,name,username'])
            ->orderBy('sort_order')
            ->orderByDesc('updated_at')
            ->get();

        $columns = [
            'todo' => $tasks->where('status', 'todo')->values(),
            'in_progress' => $tasks->where('status', 'in_progress')->values(),
            'completed' => $tasks->where('status', 'completed')->values(),
        ];

        $pendingCount = $columns['todo']->count() + $columns['in_progress']->count();
        $completedCount = $columns['completed']->count();

        $members = $project->users()
            ->select('users.id', 'users.name', 'users.username')
            ->orderBy('name')
            ->get();

        // Tasks per member
        $tasksPerMember = $members->map(function ($m) use ($tasks) {
            $count = $tasks->where('assigned_to', $m->id)->count();
            return [
                'id' => $m->id,
                'name' => $m->name,
                'username' => $m->username,
                'count' => $count,
            ];
        })->sortByDesc('count')->values();

        return view('projects.sections.board', [
            'project' => $project,
            'columns' => $columns,
            'pendingCount' => $pendingCount,
            'completedCount' => $completedCount,
            'tasksPerMember' => $tasksPerMember,
        ]);
    }

    public function move(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $data = $request->validate([
            'column' => ['required', 'in:todo,in_progress,completed'],
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer'],
        ]);

        // Ensure tasks belong to the project
        $taskIds = $data['ordered_ids'];

        $existing = Task::query()
            ->where('project_id', $project->id)
            ->whereIn('id', $taskIds)
            ->pluck('id')
            ->all();

        if (count($existing) !== count(array_unique($taskIds))) {
            return response()->json(['ok' => false, 'message' => 'Invalid tasks in request'], 422);
        }

        // Update status + sort_order based on the new order in that column
        foreach ($taskIds as $i => $taskId) {
            Task::query()
                ->where('project_id', $project->id)
                ->where('id', $taskId)
                ->update([
                    'status' => $data['column'],
                    'sort_order' => $i + 1,
                ]);
        }

        return response()->json(['ok' => true]);
    }
}
