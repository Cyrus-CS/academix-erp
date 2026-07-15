<?php

namespace App\Services\Dashboard;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Term;
use App\Support\Concerns\ResolvesAcademicPeriod;
use Illuminate\Support\Facades\DB;

class StudentDashboardService
{
    use ResolvesAcademicPeriod;

    public function build(Student $student): array
    {
        $activeYear = $this->activeAcademicYear();
        $currentTerm = $this->currentTerm($activeYear?->id);

        $grades = Grade::with('subject')
            ->where('student_id', $student->id)
            ->where('term_id', $currentTerm?->id)
            ->get();

        return [
            'student' => $student,
            'attendanceStats' => $this->attendanceStats($student),
            'grades' => $grades,
            'gradeStats' => $this->gradeStats($grades),
            'ranking' => $this->ranking($student, $currentTerm),
            'todaySchedule' => $this->todaySchedule($student),
            'payments' => $this->payments($student),
            'paymentStats' => $this->paymentStats($student),
            'announcements' => $this->announcements(),
            'lastReportCard' => $student->reportCards()->with('term')->latest()->first(),
            'activeYear' => $activeYear,
            'currentTerm' => $currentTerm,
        ];
    }

    private function attendanceStats(Student $student): array
    {
        $present = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->where('status', 'present')
            ->count();

        $absent = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->where('status', 'absent')
            ->count();

        $late = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->where('status', 'late')
            ->count();

        $total = $present + $absent + $late;

        return [
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
        ];
    }

    /**
     * NB : on distingue explicitement "pas de notes" (null) de "moyenne de 0",
     * `avg('score') ?: null` transformait à tort une moyenne de 0 en null.
     */
    private function gradeStats($grades): array
    {
        $average = $grades->avg('score');

        return [
            'average' => is_null($average) ? null : round($average, 2),
            'highest' => $grades->max('score'),
            'lowest' => $grades->min('score'),
            'total' => $grades->count(),
        ];
    }

    private function ranking(Student $student, ?Term $currentTerm): ?array
    {
        if (! $student->schoolClass) {
            return null;
        }

        $classAverages = Grade::where('term_id', $currentTerm?->id)
            ->whereIn('student_id', $student->schoolClass->students->pluck('id'))
            ->select('student_id', DB::raw('AVG(score) as avg_score'))
            ->groupBy('student_id')
            ->orderByDesc('avg_score')
            ->pluck('avg_score', 'student_id');

        $position = $classAverages->keys()->search($student->id);

        return [
            'position' => $position !== false ? $position + 1 : null,
            'total' => $classAverages->count(),
        ];
    }

    private function todaySchedule(Student $student)
    {
        if (! $student->schoolClass) {
            return collect();
        }

        return $student->schoolClass->schedules()
            ->with(['subject', 'teacher.user'])
            ->where('day_of_week', strtolower(now()->locale('fr')->dayName))
            ->orderBy('start_time')
            ->get();
    }

    private function payments(Student $student)
    {
        return Payment::with('feeType')
            ->where('student_id', $student->id)
            ->latest('payment_date')
            ->take(5)
            ->get();
    }

    private function paymentStats(Student $student): array
    {
        return [
            'paid' => Payment::where('student_id', $student->id)->where('status', 'paid')->sum('amount'),
            'pending' => Payment::where('student_id', $student->id)->where('status', 'pending')->count(),
        ];
    }

    private function announcements()
    {
        return Announcement::where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->where('audience', 'all')->orWhere('audience', 'students');
            })
            ->latest()
            ->take(3)
            ->get();
    }
}
