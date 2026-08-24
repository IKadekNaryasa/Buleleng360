<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/{resource}', [AdminController::class, 'index'])->name('resource.index');
    Route::get('/{resource}/create', [AdminController::class, 'create'])->name('resource.create');
    Route::post('/{resource}', [AdminController::class, 'store'])->name('resource.store');
    Route::get('/{resource}/{id}/edit', [AdminController::class, 'edit'])->name('resource.edit');
    Route::put('/{resource}/{id}', [AdminController::class, 'update'])->name('resource.update');
    Route::delete('/{resource}/{id}', [AdminController::class, 'destroy'])->name('resource.destroy');
});

Route::middleware(['auth', 'verified', 'operator'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/', [AdminController::class, 'operatorDashboard'])->name('dashboard');
    Route::get('/{resource}', [AdminController::class, 'index'])->name('resource.index');
    Route::get('/{resource}/create', [AdminController::class, 'create'])->name('resource.create');
    Route::post('/{resource}', [AdminController::class, 'store'])->name('resource.store');
    Route::get('/{resource}/{id}/edit', [AdminController::class, 'edit'])->name('resource.edit');
    Route::put('/{resource}/{id}', [AdminController::class, 'update'])->name('resource.update');
    Route::delete('/{resource}/{id}', [AdminController::class, 'destroy'])->name('resource.destroy');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
