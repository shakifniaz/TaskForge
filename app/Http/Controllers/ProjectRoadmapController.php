<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectRoadmapController extends Controller
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

        $milestones = Milestone::query()
            ->where('project_id', $project->id)
            // ✅ Load tasks attached to each milestone (linked feature)
            ->with(['tasks' => function ($q) use ($project) {
                // safety: ensure tasks shown are for same project
                $q->where('project_id', $project->id)
                  ->orderByRaw("CASE status WHEN 'todo' THEN 1 WHEN 'in_progress' THEN 2 WHEN 'completed' THEN 3 ELSE 4 END")
                  ->orderBy('sort_order')
                  ->orderBy('due_date')
                  ->orderByDesc('created_at');
            }])
            ->orderByRaw("CASE status WHEN 'planned' THEN 1 WHEN 'in_progress' THEN 2 WHEN 'completed' THEN 3 ELSE 4 END")
            ->orderBy('target_date')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        // Timeline grouping: YYYY-MM (by target_date, fallback to created_at month)
        $timeline = $milestones->groupBy(function ($m) {
            $date = $m->target_date ?? $m->created_at;
            return $date->format('Y-m');
        });

        return view('projects.sections.roadmap', compact('project', 'milestones', 'timeline'));
    }

    public function create(Project $project)
    {
        $this->ensureMember($project);
        return view('projects.milestones-create', compact('project'));
    }

    public function store(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'start_date' => ['nullable', 'date'],
            'target_date' => ['nullable', 'date'],
            'status' => ['required', 'in:planned,in_progress,completed'],
        ]);

        Milestone::create([
            ...$data,
            'project_id' => $project->id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('projects.roadmap', $project);
    }

    public function edit(Project $project, Milestone $milestone)
    {
        $this->ensureMember($project);
        abort_unless($milestone->project_id === $project->id, 404);

        return view('projects.milestones-edit', compact('project', 'milestone'));
    }

    public function update(Project $project, Milestone $milestone, Request $request)
    {
        $this->ensureMember($project);
        abort_unless($milestone->project_id === $project->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'start_date' => ['nullable', 'date'],
            'target_date' => ['nullable', 'date'],
            'status' => ['required', 'in:planned,in_progress,completed'],
        ]);

        $milestone->update($data);

        return redirect()->route('projects.roadmap', $project);
    }

    public function destroy(Project $project, Milestone $milestone)
    {
        $this->ensureMember($project);
        abort_unless($milestone->project_id === $project->id, 404);

        $milestone->delete();

        return redirect()->route('projects.roadmap', $project);
    }
}
