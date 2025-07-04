<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\SSOController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminReportController;

Route::get('/test-api', function () {
    return view('test-api');
});

Route::post('/test-api', [App\Http\Controllers\Auth\SSOController::class, 'testApi'])->name('test.api');

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::user()->role === 'pimpinan') {
            return redirect()->route('pimpinan.dashboard');
        }
        return redirect()->route('dashboard');
    }

    return app()->make(App\Http\Controllers\WelcomeController::class)->index();
})->name('home');



Route::get('/questionnaire/public/details/{type}', [App\Http\Controllers\PublicQuestionnaireDetailsController::class, 'show'])
    ->name('public.questionnaire.details');

// Perlu ditambahkan route untuk registrasi jika belum ada:
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Auth routes
Route::get('/login', [App\Http\Controllers\Auth\SSOController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\SSOController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\SSOController::class, 'logout'])->name('logout');

// Route publik untuk kuesioner tanpa login
Route::get('/questionnaire/public', [App\Http\Controllers\PublicQuestionnaireController::class, 'index'])
    ->name('public.questionnaire.index');

// Route untuk pengguna lulusan
Route::get('/questionnaire/public/pengguna-lulusan', [App\Http\Controllers\PublicQuestionnaireController::class, 'showPenggunaLulusanForm'])
    ->name('public.questionnaire.pengguna-lulusan');
Route::post('/questionnaire/public/pengguna-lulusan/process', [App\Http\Controllers\PublicQuestionnaireController::class, 'processPenggunaLulusanForm'])
    ->name('public.questionnaire.pengguna-lulusan.process');
Route::post('/questionnaire/public/pengguna-lulusan/{questionnaire}', [App\Http\Controllers\PublicQuestionnaireController::class, 'submitPenggunaLulusan'])
    ->name('public.questionnaire.pengguna-lulusan.submit');

// Route untuk mitra
Route::get('/questionnaire/public/mitra', [App\Http\Controllers\PublicQuestionnaireController::class, 'showMitraForm'])
    ->name('public.questionnaire.mitra');
Route::post('/questionnaire/public/mitra/process', [App\Http\Controllers\PublicQuestionnaireController::class, 'processMitraForm'])
    ->name('public.questionnaire.mitra.process');
Route::post('/questionnaire/public/mitra/{questionnaire}/submit', [App\Http\Controllers\PublicQuestionnaireController::class, 'submitMitra'])
    ->name('public.questionnaire.mitra.submit');

// Halaman terima kasih
Route::get('/questionnaire/public/thanks', [App\Http\Controllers\PublicQuestionnaireController::class, 'thanks'])
    ->name('public.questionnaire.thanks');


// Route yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update-photo');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Kuesioner - route spesifik dulu
    Route::get('/questionnaires/select', [App\Http\Controllers\QuestionnaireSelectionController::class, 'index'])
        ->name('questionnaires.select');
    Route::get('/questionnaire-details/{type}', [App\Http\Controllers\QuestionnaireDetailsController::class, 'show'])
        ->name('questionnaire.details');

    // Baru kemudian route resource
    Route::resource('questionnaires', QuestionnaireController::class);

    // Pengisian Kuesioner
    Route::get('/questionnaires/{questionnaire}/fill', [ResponseController::class, 'fill'])->name('responses.fill');
    Route::post('/questionnaires/{questionnaire}/store', [ResponseController::class, 'store'])->name('responses.store');

    // Riwayat Kuesioner
    Route::get('/responses/history', [ResponseController::class, 'history'])->name('responses.history');
    Route::get('/responses/history/{questionnaire}', [ResponseController::class, 'historyDetail'])->name('responses.history.detail');

    // Hasil Kuesioner
    Route::get('/results', [ResponseController::class, 'index'])->name('results.index');
    Route::get('/results/{questionnaire}', [ResponseController::class, 'results'])->name('results.show');

    // Admin & Pimpinan Routes
    Route::middleware(['role:admin,pimpinan'])->group(function () {
        // Laporan
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
        Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
        Route::get('/reports/export-all', [ReportController::class, 'exportAll'])->name('reports.export-all');
        Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    });

    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        // Periode Akademik
        Route::get('/academic-periods', [AcademicPeriodController::class, 'index'])->name('academic-periods.index');
        Route::post('/academic-periods', [AcademicPeriodController::class, 'store'])->name('academic-periods.store');
        Route::put('/academic-periods/{academicPeriod}', [AcademicPeriodController::class, 'update'])->name('academic-periods.update');
        Route::put('/academic-periods/{academicPeriod}/set-active', [AcademicPeriodController::class, 'setActive'])->name('academic-periods.set-active');
        Route::delete('/academic-periods/{academicPeriod}', [AcademicPeriodController::class, 'destroy'])->name('academic-periods.destroy');

        // API untuk dropdown
        Route::get('/api/questionnaires/by-period', function () {
            $academicPeriodId = request('academic_period_id');
            $questionnaires = \App\Models\Questionnaire::where('academic_period_id', $academicPeriodId)
                ->select('id', 'title')
                ->get();
            return response()->json($questionnaires);
        })->name('api.questionnaires.by-period');
    });
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

    // Import Routes
    Route::get('/import', [App\Http\Controllers\Admin\QuestionnaireImportController::class, 'index'])->name('import.index');
    Route::post('/import', [App\Http\Controllers\Admin\QuestionnaireImportController::class, 'import'])->name('import.process');
    Route::get('/import/check-log', [App\Http\Controllers\Admin\QuestionnaireImportController::class, 'checkLog'])->name('import.check-log');
    Route::get('/import/download-template', [App\Http\Controllers\Admin\QuestionnaireImportController::class, 'downloadTemplate'])->name('import.download-template');
    Route::get('/import/download-results', [App\Http\Controllers\Admin\QuestionnaireImportController::class, 'downloadResults'])->name('import.download-results');

    // Questionnaires Management
    Route::resource('questionnaires', App\Http\Controllers\Admin\AdminQuestionnaireController::class);
    Route::post('questionnaires/{questionnaire}/questions', [App\Http\Controllers\Admin\AdminQuestionnaireController::class, 'storeQuestion'])->name('questionnaires.questions.store');
    Route::post('questionnaires/{questionnaire}/questions/order', [App\Http\Controllers\Admin\AdminQuestionnaireController::class, 'updateQuestionOrder'])->name('questionnaires.questions.order');
    Route::post('questionnaires/{questionnaire}/duplicate', [App\Http\Controllers\Admin\AdminQuestionnaireController::class, 'duplicate'])->name('questionnaires.duplicate');

    // Questions Management
    Route::put('questions/{question}', [App\Http\Controllers\Admin\AdminQuestionnaireController::class, 'updateQuestion'])->name('questions.update');
    Route::delete('questions/{question}', [App\Http\Controllers\Admin\AdminQuestionnaireController::class, 'destroyQuestion'])->name('questions.destroy');

    // Results Management
    Route::get('/results', [App\Http\Controllers\Admin\AdminResultController::class, 'index'])->name('results.index');
    Route::get('/results/{questionnaire}', [App\Http\Controllers\Admin\AdminResultController::class, 'show'])->name('results.show');
    Route::get('/results/{questionnaire}/respondents', [App\Http\Controllers\Admin\AdminResultController::class, 'respondents'])->name('results.respondents');
    Route::get('/results/{questionnaire}/respondents/{user}', [App\Http\Controllers\Admin\AdminResultController::class, 'respondentDetail'])->name('results.respondent-detail');
    Route::delete('/results/{questionnaire}/respondents/{user}', [App\Http\Controllers\Admin\AdminResultController::class, 'deleteResponse'])->name('results.delete-response');

    // Respondent Categories
    Route::get('/respondent-categories', [App\Http\Controllers\Admin\AdminRespondentCategoryController::class, 'index'])->name('respondent-categories.index');
    Route::post('/respondent-categories', [App\Http\Controllers\Admin\AdminRespondentCategoryController::class, 'store'])->name('respondent-categories.store');
    Route::put('/respondent-categories/{role}', [App\Http\Controllers\Admin\AdminRespondentCategoryController::class, 'update'])->name('respondent-categories.update');

    // Schedules (Academic Periods)
    Route::get('/schedules', [App\Http\Controllers\Admin\AdminScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [App\Http\Controllers\Admin\AdminScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{academicPeriod}', [App\Http\Controllers\Admin\AdminScheduleController::class, 'update'])->name('schedules.update');
    Route::put('/schedules/{academicPeriod}/set-active', [App\Http\Controllers\Admin\AdminScheduleController::class, 'setActive'])->name('schedules.set-active');
    Route::delete('/schedules/{academicPeriod}', [App\Http\Controllers\Admin\AdminScheduleController::class, 'destroy'])->name('schedules.destroy');

    // Reports
    Route::get('/reports', [App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate/{questionnaire}', [App\Http\Controllers\Admin\AdminReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/{report}', [App\Http\Controllers\Admin\AdminReportController::class, 'show'])->name('reports.show');
    Route::put('/reports/{questionnaire}/update-content', [App\Http\Controllers\Admin\AdminReportController::class, 'updateContent'])->name('reports.update-content');
    Route::get('/reports/{report}/print', [App\Http\Controllers\Admin\AdminReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/{report}/download', [App\Http\Controllers\Admin\AdminReportController::class, 'download'])->name('reports.download');
    Route::get('/reports/{questionnaire}/export-excel', [App\Http\Controllers\Admin\AdminReportController::class, 'exportExcel'])->name('reports.export-excel');

    // User Management
    Route::get('/users', [App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [App\Http\Controllers\Admin\AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\Admin\AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('users.destroy');
});

// Pimpinan Fakultas Routes
Route::middleware(['auth', 'role:pimpinan'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Pimpinan\PimpinanDashboardController::class, 'index'])->name('dashboard');

    // Results Management
    Route::get('/results', [App\Http\Controllers\Pimpinan\PimpinanResultController::class, 'index'])->name('results.index');
    Route::get('/results/{questionnaire}', [App\Http\Controllers\Pimpinan\PimpinanResultController::class, 'show'])->name('results.show');
    Route::get('/results/{questionnaire}/respondents', [App\Http\Controllers\Pimpinan\PimpinanResultController::class, 'respondents'])->name('results.respondents');
    Route::get('/results/{questionnaire}/respondents/{user}', [App\Http\Controllers\Pimpinan\PimpinanResultController::class, 'respondentDetail'])->name('results.respondent-detail');
    Route::delete('/results/{questionnaire}/respondents/{user}', [App\Http\Controllers\Pimpinan\PimpinanResultController::class, 'deleteResponse'])->name('results.delete-response');

    // Reports
    Route::get('/reports', [App\Http\Controllers\Pimpinan\PimpinanReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [App\Http\Controllers\Pimpinan\PimpinanReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/print', [App\Http\Controllers\Pimpinan\PimpinanReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/{report}/download', [App\Http\Controllers\Pimpinan\PimpinanReportController::class, 'download'])->name('reports.download');
    Route::get('/reports/{questionnaire}/export-excel', [App\Http\Controllers\Pimpinan\PimpinanReportController::class, 'exportExcel'])->name('reports.export-excel');

    // Tambahkan route print dengan parameter orientation sebagai query parameter
    Route::get('/reports/{report}/print-landscape', function ($report) {
        return app()->make(App\Http\Controllers\Pimpinan\PimpinanReportController::class)->print($report, ['orientation' => 'landscape']);
    })->name('reports.print-landscape');
});
