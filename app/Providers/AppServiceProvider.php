<?php

namespace App\Providers;

use App\Models\Student;
use App\Models\User;
use App\Policies\MenuPolicy;
use App\Policies\StudentPolicy;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MustVerifyEmail::class, 
        User::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        
         // ── Enregistrement MenuPolicy ──────────────────────────────
        Gate::policy(User::class, MenuPolicy::class);
        Gate::policy(User::class, StudentPolicy::class);

        // ── Gates individuels pour la sidebar ─────────────────────
        Gate::define('view academic_years',       [MenuPolicy::class, 'viewAcademicYears']);
        Gate::define('view terms',                [MenuPolicy::class, 'viewTerms']);
        Gate::define('view classes',              [MenuPolicy::class, 'viewClasses']);
        Gate::define('view subjects',             [MenuPolicy::class, 'viewSubjects']);
        Gate::define('view timetables',           [MenuPolicy::class, 'viewTimetables']);
        Gate::define('view students',             [MenuPolicy::class, 'viewStudents']);
        Gate::define('view parents',              [MenuPolicy::class, 'viewParents']);
        Gate::define('view teachers',             [MenuPolicy::class, 'viewTeachers']);
        Gate::define('view teacher_contracts',    [MenuPolicy::class, 'viewTeacherContracts']);
        Gate::define('view teacher_assignments',  [MenuPolicy::class, 'viewTeacherAssignments']);
        Gate::define('view attendance',           [MenuPolicy::class, 'viewAttendance']);
        Gate::define('view grades',               [MenuPolicy::class, 'viewGrades']);
        Gate::define('view report_cards',         [MenuPolicy::class, 'viewReportCards']);
        Gate::define('view fee_types',            [MenuPolicy::class, 'viewFeeTypes']);
        Gate::define('view payments',             [MenuPolicy::class, 'viewPayments']);
        Gate::define('view announcements',        [MenuPolicy::class, 'viewAnnouncements']);
        Gate::define('view notifications',        [MenuPolicy::class, 'viewNotifications']);
        Gate::define('view reports',              [MenuPolicy::class, 'viewReports']);
        Gate::define('export data',               [MenuPolicy::class, 'exportData']);
        Gate::define('view users',                [MenuPolicy::class, 'viewUsers']);
        Gate::define('view roles',                [MenuPolicy::class, 'viewRoles']);
        Gate::define('view settings',             [MenuPolicy::class, 'viewSettings']);

        // -------------------------------------------------------------
        Gate::define('create', [Student::class, 'create']);
    }
}