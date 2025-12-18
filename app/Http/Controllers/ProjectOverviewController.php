<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Milestone;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProjectOverviewController extends Controller
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

        // Summary
        $owner = $project->owner()->select('id','name','username')->first();
        $membersCount = $project->users()->count();

        $githubRepo = trim((string)($project->github_repo ?? ''));
        $githubConnected = $githubRepo !== '';
        $repoName = $githubConnected ? preg_replace('#^https?://github\.com/#', '', $githubRepo) : null;

        // Task counts
        $totalTasks = Task::where('project_id', $project->id)->count();
        $todoCount = Task::where('project_id', $project->id)->where('status', 'todo')->count();
        $inProgressCount = Task::where('project_id', $project->id)->where('status', 'in_progress')->count();
        $completedCount = Task::where('project_id', $project->id)->where('status', 'completed')->count();

        $pendingCount = $todoCount + $inProgressCount;
        $completionRate = $totalTasks > 0 ? round(($completedCount / $totalTasks) * 100) : 0;

        $assignedToMe = Task::where('project_id', $project->id)
            ->where('assigned_to', Auth::id())
            ->count();

        // Milestones
        $milestonesTotal = Milestone::where('project_id', $project->id)->count();

        $activeMilestone = Milestone::where('project_id', $project->id)
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->first();

        // Uploads count (Project files)
        $uploadsCount = ProjectFile::where('project_id', $project->id)->count();

        // Deadlines (Overdue + Due soon)
        $today = now()->startOfDay();
        $soonEnd = now()->addDays(7)->endOfDay();

        $overdueTasks = Task::where('project_id', $project->id)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today->toDateString())
            ->with(['assignee:id,name,username'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $dueSoonTasks = Task::where('project_id', $project->id)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$today->toDateString(), $soonEnd->toDateString()])
            ->with(['assignee:id,name,username'])
            ->orderBy('due_date')
            ->limit(8)
            ->get();

        // Recent activity snapshot (use GitHub commits if connected)
        // If you already have a method in ProjectActivityController to fetch commits,
        // keep it there. For now we do a lightweight approach:
        $recentCommits = collect();
        if ($githubConnected) {
            // show via Activity page link; keep preview empty unless you already store commits
            // (keeps this safe and stable without breaking)
            $recentCommits = collect(); 
        }

        return view('projects.sections.overview', compact(
            'project',
            'owner',
            'membersCount',
            'githubConnected',
            'repoName',
            'totalTasks',
            'todoCount',
            'inProgressCount',
            'completedCount',
            'pendingCount',
            'completionRate',
            'assignedToMe',
            'milestonesTotal',
            'activeMilestone',
            'uploadsCount',
            'overdueTasks',
            'dueSoonTasks',
            'recentCommits',
        ));
    }
}
