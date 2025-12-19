<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInviteController;
use App\Http\Controllers\ProjectMembersController;
use App\Http\Controllers\ProjectSectionController;
use App\Http\Controllers\ProjectChatController;
use App\Http\Controllers\ProjectTasksController;
use App\Http\Controllers\ProjectBoardController;
use App\Http\Controllers\ProjectRoadmapController;
use App\Http\Controllers\ProjectActivityController;
use App\Http\Controllers\ProjectFilesController;
use App\Http\Controllers\ProjectOverviewController;



Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard', ['user' => Auth::user()]);
    })->name('dashboard');

    Route::get('/edit-profile', function () {
        return view('profile.edit-tab', ['user' => Auth::user()]);
    })->name('profile.edit.tab');

    Route::get('/profile', fn () => redirect()->route('dashboard'))->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/projects', [ProjectController::class, 'index'])
        ->name('projects.index');

    Route::post('/projects', [ProjectController::class, 'store'])
        ->name('projects.store');

    Route::get('/projects/{project}', [ProjectOverviewController::class, 'index'])
    ->name('projects.overview');



    Route::get('/projects/{project}/tasks', [ProjectTasksController::class, 'index'])->name('projects.tasks');
    Route::get('/projects/{project}/tasks/create', [ProjectTasksController::class, 'create'])->name('projects.tasks.create');
    Route::post('/projects/{project}/tasks', [ProjectTasksController::class, 'store'])->name('projects.tasks.store');
    Route::get('/projects/{project}/tasks/{task}/edit', [ProjectTasksController::class, 'edit'])->name('projects.tasks.edit');
    Route::patch('/projects/{project}/tasks/{task}', [ProjectTasksController::class, 'update'])->name('projects.tasks.update');
    Route::delete('/projects/{project}/tasks/{task}', [ProjectTasksController::class, 'destroy'])->name('projects.tasks.destroy');


    Route::get('/projects/{project}/board', [ProjectBoardController::class, 'index'])->name('projects.board');
    Route::post('/projects/{project}/board/move', [ProjectBoardController::class, 'move'])->name('projects.board.move');

    Route::get('/projects/{project}/roadmap', [ProjectRoadmapController::class, 'index'])->name('projects.roadmap');
    Route::get('/projects/{project}/roadmap/create', [ProjectRoadmapController::class, 'create'])->name('projects.roadmap.create');
    Route::post('/projects/{project}/roadmap', [ProjectRoadmapController::class, 'store'])->name('projects.roadmap.store');
    Route::get('/projects/{project}/roadmap/{milestone}/edit', [ProjectRoadmapController::class, 'edit'])->name('projects.roadmap.edit');
    Route::patch('/projects/{project}/roadmap/{milestone}', [ProjectRoadmapController::class, 'update'])->name('projects.roadmap.update');
    Route::delete('/projects/{project}/roadmap/{milestone}', [ProjectRoadmapController::class, 'destroy'])->name('projects.roadmap.destroy');

    Route::get('/projects/{project}/activity', [ProjectActivityController::class, 'index'])->name('projects.activity');
    Route::post('/projects/{project}/activity/connect', [ProjectActivityController::class, 'connect'])->name('projects.activity.connect');
    Route::get('/projects/{project}/activity/branches', [ProjectActivityController::class, 'branches'])->name('projects.activity.branches');
    Route::get('/projects/{project}/activity/commits', [ProjectActivityController::class, 'commits'])->name('projects.activity.commits');

    Route::get('/projects/{project}/files', [ProjectFilesController::class, 'index'])->name('projects.files');
    Route::get('/projects/{project}/files/repo', [ProjectFilesController::class, 'repoIndex'])->name('projects.files.repo');
    Route::get('/projects/{project}/files/repo/view', [ProjectFilesController::class, 'repoView'])->name('projects.files.repo.view');
    Route::get('/projects/{project}/files/repo/download', [ProjectFilesController::class, 'repoDownload'])
        ->name('projects.files.repo.download');
    Route::post('/projects/{project}/files/upload', [ProjectFilesController::class, 'upload'])->name('projects.files.upload');
    Route::get('/projects/{project}/files/{file}/download', [ProjectFilesController::class, 'download'])
        ->whereNumber('file')
        ->name('projects.files.download');
    Route::delete('/projects/{project}/files/{file}', [ProjectFilesController::class, 'destroy'])
        ->whereNumber('file')
        ->name('projects.files.destroy');


    Route::get('/projects/{project}/reports', [\App\Http\Controllers\ProjectReportsController::class, 'index'])
    ->name('projects.reports');

    Route::get('/projects/{project}/manage', [ProjectSectionController::class, 'manage'])
        ->name('projects.manage');
    Route::get('/projects/{project}/manage', [\App\Http\Controllers\ProjectManageController::class, 'index'])
    ->name('projects.manage');

    Route::patch('/projects/{project}/manage/rename', [\App\Http\Controllers\ProjectManageController::class, 'rename'])
        ->name('projects.manage.rename');

    Route::patch('/projects/{project}/manage/github', [\App\Http\Controllers\ProjectManageController::class, 'updateGithub'])
        ->name('projects.manage.github');

    Route::delete('/projects/{project}/manage/github', [\App\Http\Controllers\ProjectManageController::class, 'removeGithub'])
        ->name('projects.manage.github.remove');

    Route::delete('/projects/{project}', [\App\Http\Controllers\ProjectManageController::class, 'destroy'])
        ->name('projects.destroy');

    Route::get('/projects/{project}/members', [ProjectMembersController::class, 'index'])
        ->name('projects.members');
    Route::delete('/projects/{project}/members/{user}', [ProjectMembersController::class, 'remove'])
    ->name('projects.members.remove');

    Route::get('/projects/{project}/search-users', [ProjectInviteController::class, 'search'])
        ->name('projects.users.search');

    Route::post('/projects/{project}/add-user', [ProjectInviteController::class, 'add'])
        ->name('projects.users.add');

    Route::get('/projects/{project}/chat', [ProjectChatController::class, 'index'])
        ->name('projects.chat');
    Route::get('/projects/{project}/chat/feed', [ProjectChatController::class, 'feed'])
        ->name('projects.chat.feed');
    Route::post('/projects/{project}/chat', [ProjectChatController::class, 'store'])
        ->name('projects.chat.store');
});

require __DIR__.'/auth.php';
