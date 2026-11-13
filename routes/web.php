<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\InquiryController;
use Illuminate\Support\Facades\Route;

// 照会画面（ログイン不要）
Route::get('/', [InquiryController::class, 'index'])->name('inquiry');
Route::post('/admin/unlock', [InquiryController::class, 'unlock'])->middleware('throttle:10,1')->name('admin.unlock');
Route::post('/admin/lock', [InquiryController::class, 'lock'])->name('admin.lock');
Route::get('/pdf/summary', [InquiryController::class, 'summary'])->name('pdf.summary');
Route::get('/pdf/career', [InquiryController::class, 'career'])->name('pdf.career');

Route::middleware('admin')->group(function () {
    Route::put('/admin/profile', [AdminController::class, 'updateProfile'])->name('admin.profile');
    Route::post('/admin/skills', [AdminController::class, 'storeSkill'])->name('admin.skills.store');
    Route::put('/admin/skills', [AdminController::class, 'updateSkill'])->name('admin.skills.update');
    Route::delete('/admin/skills', [AdminController::class, 'destroySkill'])->name('admin.skills.destroy');
    Route::post('/admin/projects', [AdminController::class, 'storeProject'])->name('admin.projects.store');
    Route::put('/admin/projects/{id}', [AdminController::class, 'updateProject'])->name('admin.projects.update');
    Route::delete('/admin/projects/{id}', [AdminController::class, 'destroyProject'])->name('admin.projects.destroy');
});

// 旧画面は照会へ寄せる
Route::redirect('/home', '/');
Route::redirect('/mss', '/');
