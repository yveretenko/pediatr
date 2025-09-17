<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\DateCommentsController;
use App\Http\Controllers\Admin\DatesDisabledController;
use App\Http\Controllers\Admin\IndexController as Admin_IndexController;
use App\Http\Controllers\Admin\VaccineController;
use App\Http\Controllers\Application\AppointmentRequestController;
use App\Http\Controllers\Application\ArticleController;
use App\Http\Controllers\Application\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index']);
Route::get('/online', [IndexController::class, 'index'])->defaults('modal', 'pay_modal');
Route::get('/price', [IndexController::class, 'index'])->defaults('modal', 'price_modal');
Route::get('/nutrition-webinar', [IndexController::class, 'index'])->defaults('modal', 'nutrition_webinar_modal');
Route::get('/newborn-webinar', [IndexController::class, 'index'])->defaults('modal', 'newborn_webinar_modal');

Route::post('/article/get', [ArticleController::class, 'get']);
Route::post('/appointments/request', [AppointmentRequestController::class, 'submit'])->name('appointments.request');

Route::get('/admin', [Admin_IndexController::class, 'index'])->name('admin.index');
Route::post('/admin/index/login', [Admin_IndexController::class, 'login'])->name('admin.login');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function(){
    Route::fallback(function(){
        return response()->view('errors.404', [], 404);
    });

    Route::get('/index/logout', [Admin_IndexController::class, 'logout'])->name('admin.logout');

    Route::prefix('appointments')->group(function(){
        Route::get('/', [AppointmentController::class, 'index'])->name('appointments');
        Route::get('/filter', [AppointmentController::class, 'filter']);
        Route::post('/save', [AppointmentController::class, 'save']);
        Route::post('/delete', [AppointmentController::class, 'delete']);
        Route::get('/files', [AppointmentController::class, 'files'])->name('appointments.files');
        Route::get('/{id}/file', [AppointmentController::class, 'file'])->name('appointments.file');
        Route::post('/{id}/file-upload', [AppointmentController::class, 'fileUpload']);
        Route::get('/graph', [AppointmentController::class, 'graph']);
        Route::get('/graph-data', [AppointmentController::class, 'graphData']);
        Route::post('/history', [AppointmentController::class, 'history']);
        Route::post('/get-by-telephone', [AppointmentController::class, 'getByTelephone'])->name('appointments.getByTelephone');
    });

    Route::prefix('vaccines')->group(function(){
        Route::get('/', [VaccineController::class, 'index'])->name('vaccines.index');
        Route::get('/filter', [VaccineController::class, 'filter'])->name('vaccines.filter');
        Route::post('/save', [VaccineController::class, 'save'])->name('vaccines.save');
    });

    Route::prefix('dates-disabled')->group(function(){
        Route::get('/', [DatesDisabledController::class, 'index'])->name('dates_disabled.index');
        Route::post('/save', [DatesDisabledController::class, 'save'])->name('dates_disabled.save');
    });

    Route::post('/date-comments/save', [DateCommentsController::class, 'save']);
});
