<?php

use App\Http\Controllers\Mobile\AppController;
use App\Http\Controllers\Mobile\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('m')->name('mobile.')->group(function () {
    Route::get('/', [AppController::class, 'index'])->name('index');

    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::get('choose-platform', [AuthController::class, 'choosePlatform'])->name('choose-platform');
    Route::get('select-platform/{platform}', [AuthController::class, 'selectPlatform'])->name('select-platform');

    Route::get('dashboard', [AppController::class, 'dashboard'])->name('dashboard');
    Route::get('workspace', [AppController::class, 'workspace'])->name('workspace');
    Route::get('reports', [AppController::class, 'reports'])->name('reports');
    Route::get('alerts', [AppController::class, 'alerts'])->name('alerts');
    Route::get('histories', [AppController::class, 'histories'])->name('histories');
    Route::get('tasks', [AppController::class, 'tasks'])->name('tasks');
    Route::get('scheduler', [AppController::class, 'scheduler'])->name('scheduler');
    Route::get('facilities', [AppController::class, 'facilities'])->name('facilities');
    Route::get('facilities/{id}', [AppController::class, 'facilityDetail'])->name('facilities.show');
    Route::get('workgroups', [AppController::class, 'workgroups'])->name('workgroups');
    Route::get('workgroups/{id}', [AppController::class, 'workgroupDetail'])->name('workgroups.show');
    Route::get('workstations', [AppController::class, 'workstations'])->name('workstations');
    Route::get('workstations/{id}', [AppController::class, 'workstationDetail'])->name('workstations.show');
    Route::get('displays', [AppController::class, 'displays'])->name('displays');
    Route::get('displays/{id}', [AppController::class, 'displayDetail'])->name('displays.show');
    Route::get('notifications', [AppController::class, 'notifications'])->name('notifications');
    Route::get('search', [AppController::class, 'search'])->name('search');
    Route::get('profile', [AppController::class, 'profile'])->name('profile');
    Route::get('profile/settings', [AppController::class, 'profileSettings'])->name('profile.settings');
    Route::post('profile/settings', [AppController::class, 'profileSettings'])->name('profile.settings.update');
    Route::post('profile/remove-image', [AppController::class, 'profileRemoveImage'])->name('profile.remove-image');
});
