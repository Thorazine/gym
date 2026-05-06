<?php

use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('settings/gear', [\App\Http\Controllers\Settings\GearController::class, 'edit'])->name('settings.gear.edit');
    Route::put('settings/gear', [\App\Http\Controllers\Settings\GearController::class, 'update'])->name('settings.gear.update');

    Route::get('settings/youtube', [\App\Http\Controllers\Settings\YoutubeController::class, 'index'])->name('settings.youtube.index');
    Route::post('settings/youtube', [\App\Http\Controllers\Settings\YoutubeController::class, 'store'])->name('settings.youtube.store');
    Route::delete('settings/youtube/{video}', [\App\Http\Controllers\Settings\YoutubeController::class, 'destroy'])->name('settings.youtube.destroy');

    Route::get('settings/soundcloud', [\App\Http\Controllers\Settings\SoundcloudController::class, 'index'])->name('settings.soundcloud.index');
    Route::post('settings/soundcloud', [\App\Http\Controllers\Settings\SoundcloudController::class, 'store'])->name('settings.soundcloud.store');
    Route::delete('settings/soundcloud/{soundcloud}', [\App\Http\Controllers\Settings\SoundcloudController::class, 'destroy'])->name('settings.soundcloud.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/security', [SecurityController::class, 'edit'])->name('security.edit');

    Route::put('settings/password', [SecurityController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::inertia('settings/appearance', 'settings/Appearance')->name('appearance.edit');
});
