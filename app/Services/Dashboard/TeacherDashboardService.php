<?php

namespace App\Services\Dashboard;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Teacher;
use App\Models\TeacherContract;
use App\Support\Concerns\ResolvesAcademicPeriod;
use Illuminate\Support\Carbon;

class TeacherDashboardService
{
    use ResolvesAcademicPeriod;

    public function build(Teacher $teacher): array
    {
        $activeYear = $this->activeAcademicYear();
        $currentTerm = $this->currentTerm($activeYear?->id);

        $assignedClasses = $teacher->assignments()
            ->with(['schoolClass', 'subject'])
            ->where('academic_year_id', $activeYear?->id)
            ->get()
            ->groupBy('school_class_id');

        $myStudentIds = $assignedClasses->flatMap(
            fn ($assignments) => $assignments->first()?->schoolClass?->students->pluck('id') ?? []
        )->unique();

        return [
            'teacher' => $teacher,
            'stats' => $this->stats($teacher, $assignedClasses, $myStudentIds, $activeYear, $currentTerm),
            'assignedClasses' => $assignedClasses,
            'attendanceChart' => $this->attendanceChart($myStudentIds),
            'todaySchedule' => $teacher->schedules()
                ->with(['schoolClass', 'subject'])
                ->where('day_of_week', strtolower(now()->locale('fr')->dayName))
                ->orderBy('start_time')
                ->get(),
            'recentGrades' => Grade::with(['student.user', 'subject', 'schoolClass'])
                ->where('teacher_id', $teacher->id)
                ->latest()
                ->take(6)
                ->get(),
            'announcements' => $this->announcements(),
            'activeContract' => TeacherContract::where('teacher_id', $teacher->id)
                ->where('status', 'active')
                ->first(),
            'activeYear' => $activeYear,
            'currentTerm' => $currentTerm,
        ];
    }

    private function stats(Teacher $teacher, $assignedClasses, $myStudentIds, $activeYear, $currentTerm): array
    {
        $todayAttendance = Attendance::whereIn('student_id', $myStudentIds)
            ->whereDate('date', today())
            ->get();

        return [
            'my_classes' => $assignedClasses->count(),
            'my_students' => $myStudentIds->count(),
            // NB : Builder::distinct('colonne')->count() n'est pas fiable selon les
            // versions d'Eloquent (le paramètre de distinct() est ignoré par count()).
            // On calcule le nombre de matières distinctes en collection, sans ambiguïté.
            'my_subjects' => $teacher->assignments()
                ->where('academic_year_id', $activeYear?->id)
                ->pluck('subject_id')
                ->unique()
                ->count(),
            'present_today' => $todayAttendance->where('status', 'present')->count(),
            'absent_today' => $todayAttendance->where('status', 'absent')->count(),
            'grades_this_term' => Grade::where('teacher_id', $teacher->id)
                ->where('term_id', $currentTerm?->id)
                ->count(),
        ];
    }

    /**
     * Présences des 7 derniers jours pour les classes de cet enseignant.
     */
    private function attendanceChart($myStudentIds)
    {
        return collect(range(6, 0))->map(function ($daysAgo) use ($myStudentIds) {
            $date = Carbon::today()->subDays($daysAgo);
            $records = Attendance::whereIn('student_id', $myStudentIds)
                ->whereDate('date', $date)
                ->get();

            return [
                'date' => $date->translatedFormat('D d/m'),
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
            ];
        });
    }

    private function announcements()
    {
        return Announcement::where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->where('audience', 'all')->orWhere('audience', 'teachers');
            })
            ->latest()
            ->take(3)
            ->get();
    }
}
