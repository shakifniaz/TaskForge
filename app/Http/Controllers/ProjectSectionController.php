<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectSectionController extends Controller
{
    private function ensureMember(Project $project): void
    {
        abort_unless(
            $project->users()->where('users.id', Auth::id())->exists(),
            403
        );
    }

    public function overview(Project $project)
    {
        $this->ensureMember($project);

        return view('projects.sections.overview', compact('project'));
    }

    public function chat(Project $project)
    {
        $this->ensureMember($project);

        return view('projects.sections.chat', compact('project'));
    }

    public function tasks(Project $project)
    {
        $this->ensureMember($project);

        return view('projects.sections.tasks', compact('project'));
    }

    public function board(Project $project)
    {
        $this->ensureMember($project);

        return view('projects.sections.board', compact('project'));
    }

    public function roadmap(Project $project)
    {
        $this->ensureMember($project);

        return view('projects.sections.roadmap', compact('project'));
    }

    public function activity(Project $project)
    {
        $this->ensureMember($project);

        return view('projects.sections.activity', compact('project'));
    }

    public function files(Project $project)
    {
        $this->ensureMember($project);

        return view('projects.sections.files', compact('project'));
    }

    public function reports(Project $project)
    {
        $this->ensureMember($project);

        return view('projects.sections.reports', compact('project'));
    }

    public function manage(Project $project)
    {
        $this->ensureMember($project);

        return view('projects.sections.manage', compact('project'));
    }
}
