<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification
Auth::routes();

// Routes protégées
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // ==================== PROJETS ====================
    Route::resource('projects', ProjectController::class);
    
    // ==================== TÂCHES ====================
    // Route resource pour tasks (gère index, create, store, show, edit, update, destroy)
    Route::resource('tasks', TaskController::class);
    
    // Route spécifique pour la création d'une tâche depuis un projet (POST)
    Route::post('/projects/{project}/tasks', [TaskController::class, 'storeFromProject'])->name('projects.tasks.store');
    
    // Route pour la mise à jour du statut via AJAX
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
    
    // ==================== COMMENTAIRES ====================
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
    
    // ==================== ADMIN ====================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::put('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.update-role');
        Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
    });
    Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [App\Http\Controllers\ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::put('/update-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('update-password');
    Route::get('/statistics', [App\Http\Controllers\ProfileController::class, 'statistics'])->name('statistics');
});
});