<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ServiceController;
use App\Http\Middleware\VerifyRecaptcha;
use App\Http\Middleware\EnsureUserIsApproved;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Fortify;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/survey/select', [SurveyController::class, 'selectCity'])->name('survey.selectCity');
// Setelah memilih kota (mpp), user memilih instansi induk (jika ingin kombinasi)
Route::get('/survey/select-institution/{slug}', [SurveyController::class, 'selectInstitution'])->name('survey.selectInstitution');
Route::get('/survey/form/{slug}', [SurveyController::class, 'form'])->name('survey.form');
Route::post('/survey/submit/{slug}', [SurveyController::class, 'submit']) ->middleware([VerifyRecaptcha::class])->name('survey.submit');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest', 'throttle:login', VerifyRecaptcha::class])
    ->name('login');

Route::post('/register', [RegisteredUserController::class, 'store'])->middleware(['guest', 'throttle:register', VerifyRecaptcha::class])->name('register');
Route::middleware(['auth', 'verified','approved', 'role:admin_instansi'])->group(function () {
    Route::get('/dashboard/instansi', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/instansi/services', ServiceController::class)->names([
            'index' => 'instansi.services.index',
            'create' => 'instansi.services.create',
            'store' => 'instansi.services.store',
            'show' => 'instansi.services.show',
            'edit' => 'instansi.services.edit',
            'update' => 'instansi.services.update',
            'destroy' => 'instansi.services.destroy',
        ]);
});

Route::middleware(['auth', 'verified', 'approved', 'role:super_admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::put('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
    Route::get('/instansi', [InstitutionController::class, 'index'])->name('institutions.index');
    Route::get('/instansi/create', [InstitutionController::class, 'create'])->name('institutions.create');
    Route::post('/instansi', [InstitutionController::class, 'store'])->name('institutions.store');
    Route::get('/instansi/{institution}/edit', [InstitutionController::class, 'edit'])->name('institutions.edit');
    Route::put('/instansi/{institution}', [InstitutionController::class, 'update'])->name('institutions.update');
    Route::delete('/instansi/{institution}', [InstitutionController::class, 'destroy'])->name('institutions.destroy');
    Route::get('/services/{institution}', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create/{institution}', [ServiceController::class, 'create']);
    Route::resource('/services', ServiceController::class);
});
