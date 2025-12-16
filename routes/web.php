<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInviteController;
use App\Http\Controllers\ProjectMembersController;
use App\Http\Controllers\ProjectSectionController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/edit-profile', function () {
    return view('profile.edit-tab', ['user' => Auth::user()]);
})->middleware(['auth'])->name('profile.edit.tab');

Route::get('/dashboard', function () {
    return view('dashboard', ['user' => Auth::user()]);
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', fn () => redirect()->route('dashboard'))->name('profile.edit');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');

    // Default project page = Overview
    Route::get('/projects/{project}', [ProjectSectionController::class, 'overview'])->name('projects.overview');

    // Sidebar sections (empty placeholders for now)
    Route::get('/projects/{project}/chat', [ProjectSectionController::class, 'chat'])->name('projects.chat');
    Route::get('/projects/{project}/tasks', [ProjectSectionController::class, 'tasks'])->name('projects.tasks');
    Route::get('/projects/{project}/board', [ProjectSectionController::class, 'board'])->name('projects.board');
    Route::get('/projects/{project}/roadmap', [ProjectSectionController::class, 'roadmap'])->name('projects.roadmap');
    Route::get('/projects/{project}/activity', [ProjectSectionController::class, 'activity'])->name('projects.activity');
    Route::get('/projects/{project}/files', [ProjectSectionController::class, 'files'])->name('projects.files');
    Route::get('/projects/{project}/reports', [ProjectSectionController::class, 'reports'])->name('projects.reports');

    // Members page (contains members list + invite)
    Route::get('/projects/{project}/members', [ProjectMembersController::class, 'index'])->name('projects.members');

    // Manage project page (empty for now)
    Route::get('/projects/{project}/manage', [ProjectSectionController::class, 'manage'])->name('projects.manage');

    // Invite/search endpoints used by Members page
    Route::get('/projects/{project}/search-users', [ProjectInviteController::class, 'search'])->name('projects.users.search');
    Route::post('/projects/{project}/add-user', [ProjectInviteController::class, 'add'])->name('projects.users.add');
});

require __DIR__.'/auth.php';
