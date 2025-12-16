<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInviteController;
use App\Http\Controllers\ProjectMembersController;
use App\Http\Controllers\ProjectSectionController;
use App\Http\Controllers\ProjectChatController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Profile (non-project)
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Projects
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    | Projects list + create
    */
    Route::get('/projects', [ProjectController::class, 'index'])
        ->name('projects.index');

    Route::post('/projects', [ProjectController::class, 'store'])
        ->name('projects.store');

    /*
    | Default project page → Overview
    */
    Route::get('/projects/{project}', [ProjectSectionController::class, 'overview'])
        ->name('projects.overview');

    /*
    | Sidebar sections (empty placeholders for now)
    */
    Route::get('/projects/{project}/tasks', [ProjectSectionController::class, 'tasks'])
        ->name('projects.tasks');

    Route::get('/projects/{project}/board', [ProjectSectionController::class, 'board'])
        ->name('projects.board');

    Route::get('/projects/{project}/roadmap', [ProjectSectionController::class, 'roadmap'])
        ->name('projects.roadmap');

    Route::get('/projects/{project}/activity', [ProjectSectionController::class, 'activity'])
        ->name('projects.activity');

    Route::get('/projects/{project}/files', [ProjectSectionController::class, 'files'])
        ->name('projects.files');

    Route::get('/projects/{project}/reports', [ProjectSectionController::class, 'reports'])
        ->name('projects.reports');

    Route::get('/projects/{project}/manage', [ProjectSectionController::class, 'manage'])
        ->name('projects.manage');

    /*
    | Members
    */
    Route::get('/projects/{project}/members', [ProjectMembersController::class, 'index'])
        ->name('projects.members');

    Route::get('/projects/{project}/search-users', [ProjectInviteController::class, 'search'])
        ->name('projects.users.search');

    Route::post('/projects/{project}/add-user', [ProjectInviteController::class, 'add'])
        ->name('projects.users.add');

    /*
    | Project Chat (REAL chat controller)
    */
    Route::get('/projects/{project}/chat', [ProjectChatController::class, 'index'])
        ->name('projects.chat');

    Route::get('/projects/{project}/chat/feed', [ProjectChatController::class, 'feed'])
        ->name('projects.chat.feed');

    Route::post('/projects/{project}/chat', [ProjectChatController::class, 'store'])
        ->name('projects.chat.store');
});

/*
|--------------------------------------------------------------------------
| Auth scaffolding
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
