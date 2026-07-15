<?php

use App\Http\Controllers\Academic\AcademicYearController;
use App\Http\Controllers\Academic\SchedulesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Evaluation\AttendanceController;
use App\Http\Controllers\Evaluation\GradeController;
use App\Http\Controllers\Evaluation\ReportCardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques (auth non requise)
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Routes protégées :
|   - auth  → utilisateur connecté
|   - verified   → email validé
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | DASHBOARD
    |----------------------------------------------------------------------
    | Une seule route d'entrée → le contrôleur redirige selon le rôle.
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | AJAX : Rafraîchir les statistiques du dashboard (admin only)
    |----------------------------------------------------------------------
    */
    Route::post('/dashboard/refresh-stats', [DashboardController::class, 'refreshStats'])
        ->middleware('role:Admin')
        ->name('dashboard.refresh-stats');

    /*
    |----------------------------------------------------------------------
    | ACADEMIC
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin')->group(function () {
        Route::resource('academic-years', AcademicYearController::class)->except(['show']);
        Route::resource('terms', \App\Http\Controllers\Academic\TermController::class);
        Route::resource('classes', \App\Http\Controllers\Academic\ClassController::class);
        Route::resource('subjects', \App\Http\Controllers\Academic\SubjectController::class);
        Route::resource('timetables', SchedulesController::class);
        Route::resource('teacher-assignments', \App\Http\Controllers\Academic\TeacherAssignmentController::class);
        
        // Déplacer un créneau via drag & drop
        Route::patch('timetables/{schedule}/move', [SchedulesController::class, 'move'])
            ->name('timetables.move');
            
            Route::post('classes/reorder', [\App\Http\Controllers\Academic\ClassController::class, 'reorder'])
                ->name('classes.reorder');
        
        Route::post('academic-years/reorder', [AcademicYearController::class, 'reorder'])
                ->name('academic-years.reorder');
    });

    /*
    |----------------------------------------------------------------------
    | STUDENTS
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin')->group(function () {
        Route::resource('students', StudentController::class);
        Route::resource('parents', \App\Http\Controllers\Student\ParentController::class);
    });

    /*
    |----------------------------------------------------------------------
    | TEACHERS
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin')->group(function () {
        Route::resource('teachers', \App\Http\Controllers\Teacher\TeacherController::class);
        Route::resource('teacher-contracts', \App\Http\Controllers\Teacher\TeacherContractController::class);
    });

    /*
    |----------------------------------------------------------------------
    | ATTENDANCE
    |   - Admin : accès complet
    |   - Teacher : saisie et consultation de ses classes
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin|Teacher')->group(function () {
        Route::resource('attendance', AttendanceController::class);
    });

    /*
    |----------------------------------------------------------------------
    | GRADES
    |   - Admin : accès complet
    |   - Teacher : saisie et consultation de ses matières
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin|Teacher')->group(function () {
        Route::resource('grades', GradeController::class);
    });

    /*
    |----------------------------------------------------------------------
    | REPORT CARDS
    |   - Admin & Teacher : génération
    |   - Student & Parent : consultation uniquement
    |----------------------------------------------------------------------
    */
    Route::resource('report-cards', ReportCardController::class)
        ->middleware('role:Admin|Teacher|Student|Parent');
    // Télécharger un bulletin PDF
    Route::get('report-cards/{reportCard}/download', [ReportCardController::class, 'download'])
        ->name('report-cards.download');

    // Générer tous les bulletins du trimestre actif
    Route::post('report-cards/generate-all', [ReportCardController::class, 'generateAll'])
        ->name('report-cards.generate-all')
        ->middleware('role:Admin|Teacher');

    /*
    |----------------------------------------------------------------------
    | FINANCE
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin')->group(function () {
        Route::resource('fee-types', \App\Http\Controllers\Finance\FeeTypeController::class);
        Route::resource('payments', \App\Http\Controllers\Finance\PaymentController::class);
    });

    /*
    |----------------------------------------------------------------------
    | COMMUNICATION
    |----------------------------------------------------------------------
    */
    Route::resource('announcements', \App\Http\Controllers\Communication\AnnouncementController::class)
        ->middleware('role:Admin');

     // Renouveler une annonce expirée
    Route::patch('announcements/{announcement}/renew', [\App\Http\Controllers\Communication\AnnouncementController::class, 'renew'])
        ->name('announcements.renew');

    // Réordonner (SortableJS)
    Route::post('announcements/reorder', [\App\Http\Controllers\Communication\AnnouncementController::class, 'reorder'])
        ->name('announcements.reorder');

    Route::get('/notifications', [\App\Http\Controllers\Communication\NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Communication\NotificationController::class, 'markAllRead'])
        ->name('notifications.markAllRead');

    Route::get('/notifications/unread-count', [
        \App\Http\Controllers\Communication\NotificationController::class, 'unreadCount'
    ])->name('notifications.unread-count');
        
    Route::get('/notifications/latest', [
        \App\Http\Controllers\Communication\NotificationController::class, 'latest'
    ])->name('notifications.latest');

    Route::patch('/notifications/{id}/mark-read', [
        \App\Http\Controllers\Communication\NotificationController::class, 'markAsRead'
    ])->name('notifications.markAsRead');

    Route::delete('/notifications/delete-read', [
        \App\Http\Controllers\Communication\NotificationController::class, 'destroyRead'
    ])->name('notifications.destroyRead');

    /*
    |----------------------------------------------------------------------
    | REPORTS & EXPORTS
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin')->group(function () {
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/exports', [\App\Http\Controllers\Admin\ExportController::class, 'index'])
            ->name('exports.index');

        Route::get('/exports/students', [\App\Http\Controllers\Admin\ExportController::class, 'students'])
            ->name('exports.students');

        Route::get('/exports/teachers', [\App\Http\Controllers\Admin\ExportController::class, 'teachers'])
            ->name('exports.teachers');

        Route::get('/exports/grades', [\App\Http\Controllers\Admin\ExportController::class, 'grades'])
            ->name('exports.grades');

        Route::get('/exports/attendance', [\App\Http\Controllers\Admin\ExportController::class, 'attendance'])
            ->name('exports.attendance');

        Route::get('/exports/payments', [\App\Http\Controllers\Admin\ExportController::class, 'payments'])
            ->name('exports.payments');
    });

    /*
    |----------------------------------------------------------------------
    | ADMINISTRATION (admin only)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:Admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('roles', \App\Http\Controllers\Admin\RolePermissionController::class);
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])
            ->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])
            ->name('settings.update');
    });

    /*
    |----------------------------------------------------------------------
    | AIDE
    |----------------------------------------------------------------------
    */
    Route::get('/help', fn() => view('help.index'))->name('help.index');

    /*
    |----------------------------------------------------------------------
    | API INTERNE : Recherche globale
    |----------------------------------------------------------------------
    */
    Route::get('/api/search', [\App\Http\Controllers\API\SearchController::class, 'search'])
        ->name('api.search');

});

/*
|--------------------------------------------------------------------------
| Authentification (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});