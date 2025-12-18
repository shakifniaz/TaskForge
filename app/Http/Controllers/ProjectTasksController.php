<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTasksController extends Controller
{
    private function ensureMember(Project $project): void
    {
        abort_unless(
            $project->users()->where('users.id', Auth::id())->exists(),
            403
        );
    }

    private function projectMilestones(Project $project)
    {
        return Milestone::query()
            ->where('project_id', $project->id)
            ->orderBy('target_date')
            ->orderBy('created_at')
            ->get(['id', 'title', 'status', 'target_date']);
    }

    public function index(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $query = Task::query()
            ->where('project_id', $project->id)
            ->with([
                'assignee:id,name,username',
                'creator:id,name,username',
                'milestone:id,title',
            ])
            ->orderByRaw("CASE status WHEN 'todo' THEN 1 WHEN 'in_progress' THEN 2 WHEN 'completed' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->orderByDesc('created_at');

        // Filters
        if ($request->boolean('mine')) {
            $query->where('assigned_to', Auth::id());
        }

        if ($request->get('due') === 'soon') {
            $query->whereNotNull('due_date')
                ->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()]);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        // Milestone filter:
        // - milestone_id=0  => tasks without milestone
        // - milestone_id=ID => tasks attached to that milestone
        if ($request->has('milestone_id') && $request->string('milestone_id') === '0') {
            $query->whereNull('milestone_id');
        } elseif ($request->filled('milestone_id')) {
            $query->where('milestone_id', $request->integer('milestone_id'));
        }

        $tasks = $query->get();

        // Members for assignment dropdown
        $members = $project->users()
            ->select('users.id', 'users.name', 'users.username')
            ->orderBy('name')
            ->get();

        // Milestones for filter dropdown
        $milestones = $this->projectMilestones($project);

        return view('projects.sections.tasks', compact('project', 'tasks', 'members', 'milestones'));
    }

    public function create(Project $project)
    {
        $this->ensureMember($project);

        $members = $project->users()
            ->select('users.id', 'users.name', 'users.username')
            ->orderBy('name')
            ->get();

        $milestones = $this->projectMilestones($project);

        return view('projects.tasks-create', compact('project', 'members', 'milestones'));
    }

    public function store(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'integer'],
            'milestone_id' => ['nullable', 'integer'],
            'status' => ['required', 'in:todo,in_progress,completed'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
        ]);

        // Ensure assignee is a member of this project (or null)
        if (!empty($data['assigned_to'])) {
            $isMember = $project->users()->where('users.id', $data['assigned_to'])->exists();
            if (!$isMember) {
                return back()->withErrors(['assigned_to' => 'Selected user is not a project member.'])->withInput();
            }
        }

        // Ensure milestone belongs to this project (or null)
        if (!empty($data['milestone_id'])) {
            $ok = Milestone::where('id', $data['milestone_id'])
                ->where('project_id', $project->id)
                ->exists();

            if (!$ok) {
                return back()->withErrors(['milestone_id' => 'Selected milestone is not in this project.'])->withInput();
            }
        }

        Task::create([
            ...$data,
            'project_id' => $project->id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('projects.tasks', $project);
    }

    public function edit(Project $project, Task $task)
    {
        $this->ensureMember($project);
        abort_unless($task->project_id === $project->id, 404);

        $members = $project->users()
            ->select('users.id', 'users.name', 'users.username')
            ->orderBy('name')
            ->get();

        $milestones = $this->projectMilestones($project);

        return view('projects.tasks-edit', compact('project', 'task', 'members', 'milestones'));
    }

    public function update(Project $project, Task $task, Request $request)
    {
        $this->ensureMember($project);
        abort_unless($task->project_id === $project->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'integer'],
            'milestone_id' => ['nullable', 'integer'],
            'status' => ['required', 'in:todo,in_progress,completed'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
        ]);

        if (!empty($data['assigned_to'])) {
            $isMember = $project->users()->where('users.id', $data['assigned_to'])->exists();
            if (!$isMember) {
                return back()->withErrors(['assigned_to' => 'Selected user is not a project member.'])->withInput();
            }
        }

        if (!empty($data['milestone_id'])) {
            $ok = Milestone::where('id', $data['milestone_id'])
                ->where('project_id', $project->id)
                ->exists();

            if (!$ok) {
                return back()->withErrors(['milestone_id' => 'Selected milestone is not in this project.'])->withInput();
            }
        }

        $task->update($data);

        return redirect()->route('projects.tasks', $project);
    }

    public function destroy(Project $project, Task $task)
    {
        $this->ensureMember($project);
        abort_unless($task->project_id === $project->id, 404);

        $task->delete();

        return redirect()->route('projects.tasks', $project);
    }
}
