<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

Route::middleware(['auth'])->group(function () {
    Route::get('/users', [AdminController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
    Route::post('/users/{user}/update-group', [AdminController::class, 'updateGroup'])->name('admin.users.updateGroup');
    Route::patch('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');

    Route::get('/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::post('/upload', [FileController::class, 'upload'])->name('files.upload');
    Route::get('/download/{file}', [FileController::class, 'download'])->name('files.download');
    Route::delete('/delete/{file}', [FileController::class, 'delete'])->name('files.delete');

    Route::post('/folders', [FileController::class, 'createFolder'])->name('folders.create');
    Route::get('/folders/{folder?}', [FileController::class, 'viewFolder'])->where('folder', '.*')->name('folders.view');
});

Route::get('/', [FileController::class, 'index'])->name('files.index');

Route::get('/debug', function () {
    return auth()->user();
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
