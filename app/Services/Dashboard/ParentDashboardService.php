<?php

namespace App\Services\Dashboard;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Support\Concerns\ResolvesAcademicPeriod;

class ParentDashboardService
{
    use ResolvesAcademicPeriod;

    public function build(ParentProfile $parent): array
    {
        $activeYear = $this->activeAcademicYear();
        $currentTerm = $this->currentTerm($activeYear?->id);

        $children = $parent->students()->with(['user', 'schoolClass'])->get();
        $childrenIds = $children->pluck('id');

        return [
            'parent' => $parent,
            'children' => $children,
            'childrenSummary' => $children->map(fn ($child) => $this->childSummary($child, $currentTerm)),
            'recentAbsences' => Attendance::with(['student.user', 'schoolClass'])
                ->whereIn('student_id', $childrenIds)
                ->where('status', 'absent')
                ->latest('date')
                ->take(6)
                ->get(),
            'recentPayments' => Payment::with(['student.user', 'feeType'])
                ->whereIn('student_id', $childrenIds)
                ->latest('payment_date')
                ->take(5)
                ->get(),
            'pendingPayments' => Payment::with(['student.user', 'feeType'])
                ->whereIn('student_id', $childrenIds)
                ->where('status', 'pending')
                ->get(),
            'recentGrades' => Grade::with(['student.user', 'subject'])
                ->whereIn('student_id', $childrenIds)
                ->where('term_id', $currentTerm?->id)
                ->latest()
                ->take(6)
                ->get(),
            'announcements' => $this->announcements(),
            'activeYear' => $activeYear,
            'currentTerm' => $currentTerm,
        ];
    }

    private function childSummary($child, $currentTerm): array
    {
        $attendance = Attendance::where('student_id', $child->id)
            ->whereMonth('date', now()->month)
            ->get();

        $avgGrade = Grade::where('student_id', $child->id)
            ->where('term_id', $currentTerm?->id)
            ->avg('score');

        return [
            'student' => $child,
            'attendance_rate' => $attendance->count() > 0
                ? round(($attendance->where('status', 'present')->count() / $attendance->count()) * 100, 1)
                : null,
            'absences' => $attendance->where('status', 'absent')->count(),
            'avg_grade' => is_null($avgGrade) ? null : round($avgGrade, 2),
            'pending_payments' => Payment::where('student_id', $child->id)
                ->where('status', 'pending')
                ->count(),
        ];
    }

    private function announcements()
    {
        return Announcement::where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->where('audience', 'all')->orWhere('audience', 'parents');
            })
            ->latest()
            ->take(3)
            ->get();
    }
}
