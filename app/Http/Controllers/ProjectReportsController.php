<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Milestone;

class ProjectReportsController extends Controller
{
    private function ensureMember(Project $project): void
    {
        abort_unless(
            $project->users()->where('users.id', Auth::id())->exists(),
            403
        );
    }

    public function index(Project $project, Request $request)
    {
        $this->ensureMember($project);

        $days = (int)($request->get('days', 30));
        if (!in_array($days, [7, 30, 90], true)) {
            $days = 30;
        }

        $from = now()->subDays($days)->startOfDay();
        $to = now()->endOfDay();

        $completedPerWeek = Task::query()
            ->where('project_id', $project->id)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$from, $to])
            ->selectRaw("
                strftime('%Y-%W', updated_at) as year_week,
                COUNT(*) as count
            ")
            ->groupBy('year_week')
            ->orderBy('year_week')
            ->get()
            ->map(function ($row) {
                return [
                    'year_week' => $row->year_week,
                    'count' => (int)$row->count,
                ];
            })
            ->values();

        $assignedCounts = Task::query()
            ->where('project_id', $project->id)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('assigned_to')
            ->selectRaw("assigned_to as user_id, COUNT(*) as assigned_count")
            ->groupBy('assigned_to')
            ->get()
            ->keyBy('user_id');

        $completedCounts = Task::query()
            ->where('project_id', $project->id)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$from, $to])
            ->whereNotNull('assigned_to')
            ->selectRaw("assigned_to as user_id, COUNT(*) as completed_count")
            ->groupBy('assigned_to')
            ->get()
            ->keyBy('user_id');

        $members = $project->users()
            ->select('users.id', 'users.name', 'users.username')
            ->orderBy('name')
            ->get();

        $tasksPerMember = $members->map(function ($m) use ($assignedCounts, $completedCounts) {
            $assigned = (int)optional($assignedCounts->get($m->id))->assigned_count;
            $completed = (int)optional($completedCounts->get($m->id))->completed_count;

            return [
                'id' => $m->id,
                'name' => $m->name,
                'username' => $m->username,
                'assigned' => $assigned,
                'completed' => $completed,
            ];
        })->values();

        $completedWithDue = Task::query()
            ->where('project_id', $project->id)
            ->where('status', 'completed')
            ->whereNotNull('due_date')
            ->whereBetween('updated_at', [$from, $to])
            ->count();

        $onTimeCompleted = Task::query()
            ->where('project_id', $project->id)
            ->where('status', 'completed')
            ->whereNotNull('due_date')
            ->whereBetween('updated_at', [$from, $to])
            ->whereRaw("date(updated_at) <= due_date")
            ->count();

        $onTimeRate = $completedWithDue > 0
            ? round(($onTimeCompleted / $completedWithDue) * 100)
            : 0;

        $milestones = Milestone::query()
            ->where('project_id', $project->id)
            ->orderBy('due_date')
            ->get();

        $milestoneSummary = $milestones->map(function ($ms) use ($project) {
            $total = Task::query()
                ->where('project_id', $project->id)
                ->where('milestone_id', $ms->id)
                ->count();

            $done = Task::query()
                ->where('project_id', $project->id)
                ->where('milestone_id', $ms->id)
                ->where('status', 'completed')
                ->count();

            $pct = $total > 0 ? round(($done / $total) * 100) : 0;

            return [
                'id' => $ms->id,
                'title' => $ms->title,
                'due_date' => $ms->due_date,
                'total' => $total,
                'done' => $done,
                'pct' => $pct,
            ];
        })->values();

        return view('projects.sections.reports', compact(
            'project',
            'days',
            'from',
            'to',
            'completedPerWeek',
            'tasksPerMember',
            'onTimeRate',
            'completedWithDue',
            'onTimeCompleted',
            'milestoneSummary'
        ));
    }
}
