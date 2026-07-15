<?php

namespace App\Policies;

use App\Models\User;

class MenuPolicy
{
    // ── Académique ────────────────────────────────────────────────
    public function viewAcademicYears(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function viewTerms(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function viewClasses(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Teacher']);
    }

    public function viewSubjects(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Teacher']);
    }

    public function viewTimetables(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Teacher', 'Student', 'Parent']);
    }

    // ── Élèves ────────────────────────────────────────────────────
    public function viewStudents(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Teacher']);
    }

    public function viewParents(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    // ── Enseignants ───────────────────────────────────────────────
    public function viewTeachers(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function viewTeacherContracts(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function viewTeacherAssignments(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    // ── Évaluations ───────────────────────────────────────────────
    public function viewAttendance(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Teacher']);
    }

    public function viewGrades(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Teacher']);
    }

    public function viewReportCards(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Teacher', 'Student', 'Parent']);
    }

    // ── Finances ─────────────────────────────────────────────────
    public function viewFeeTypes(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function viewPayments(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Student', 'Parent']);
    }

    // ── Communication ─────────────────────────────────────────────
    public function viewAnnouncements(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Teacher', 'Student', 'Parent']);
    }

    public function viewNotifications(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Teacher', 'Student', 'Parent']);
    }

    // ── Rapports ─────────────────────────────────────────────────
    public function viewReports(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function exportData(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    // ── Administration ────────────────────────────────────────────
    public function viewUsers(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function viewRoles(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function viewSettings(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}