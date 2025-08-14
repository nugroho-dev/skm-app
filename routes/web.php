<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\QuestionerController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\OccupationController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\VerifyRecaptcha;
use App\Http\Middleware\EnsureUserIsApproved;
use App\Models\Occupation;
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

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
  
    // Super Admin routes
    Route::middleware('role:super_admin')->group(function () {
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
        //Route::get('/services/{institution}', [ServiceController::class, 'index'])->name('services.index');
        //Route::get('/services/create/{institution}', [ServiceController::class, 'create'])->name('services.create');
        Route::resource('/service', ServiceController::class);
        Route::get('/profile', function () {
            $title = 'Profil Pengguna';
            return view('dashboard.profile.index', compact('title'));
        })->name('profile.show');
        Route::get('/questioner', [QuestionerController::class, 'index'])->name('questioner.index');
        Route::get('/question', [QuestionController::class, 'index'])->name('question.index');
        Route::get('/question/create', [QuestionController::class, 'create'])->name('question.create');
        Route::post('/question', [QuestionController::class, 'store'])->name('question.store');
        Route::get('/question/{unsur}/edit/{question}', [QuestionController::class, 'edit'])->name('question.edit');
        Route::put('/question/{unsur}/update/{question}', [QuestionController::class, 'update'])->name('question.update');
        Route::delete('/question/{unsur}/delete/{question}', [QuestionController::class, 'destroy'])->name('question.destroy');
        Route::resource('pendidikan', EducationController::class)->names([
            'index' => 'pendidikan.index',
            'create' => 'pendidikan.create',
            'store' => 'pendidikan.store',
            'edit' => 'pendidikan.edit',
            'update' => 'pendidikan.update',
            'destroy' => 'pendidikan.destroy',
      ]);
        Route::resource('pekerjaan', OccupationController::class)->names([
            'index' => 'pekerjaan.index',
            'create' => 'pekerjaan.create',
            'store' => 'pekerjaan.store',
            'edit' => 'pekerjaan.edit',
            'update' => 'pekerjaan.update',
            'destroy' => 'pekerjaan.destroy',
        ]);
        Route::resource('unsur', ElementController::class)->names([
            'index' => 'unsur.index',
            'create' => 'unsur.create',
            'store' => 'unsur.store',
            'edit' => 'unsur.edit',
            'update' => 'unsur.update',
            'destroy' => 'unsur.destroy',
        ]);
        Route::get('/laporan-skm', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan-ikm/pdf', [ReportController::class, 'cetakPdf'])->name('ikm.cetak.pdf');
        Route::get('/laporan-ikm/publikasi/cetakPublikasiPdf', [ReportController::class, 'cetakPublikasiPdf'])->name('ikm.cetak.publikasi.pdf');
    });

    // Admin Instansi routes
    Route::middleware('role:admin_instansi')->group(function () {
        Route::get('/dashboard/instansi', [DashboardController::class, 'index'])->name('instansi.dashboard');
        Route::resource('/instansi/service', ServiceController::class)->names([
            'index' => 'instansi.services.index',
            'create' => 'instansi.services.create',
            'store' => 'instansi.services.store',
            'show' => 'instansi.services.show',
            'edit' => 'instansi.services.edit',
            'update' => 'instansi.services.update',
            'destroy' => 'instansi.services.destroy',
        ]);
        Route::get('/instansi/profile', function () {
            $title = 'Profil Pengguna';
            return view('dashboard.profile.index', compact('title'));
        })->name('instansi.profile.show');
    });
});