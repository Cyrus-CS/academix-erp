<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classe;
use App\Models\Grade;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index(Request $request): View
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $currentTerm = Term::where('is_current', true)->first();

        // Statistiques générales
        $stats = $this->getGlobalStats($currentYear);

        // Données pour les graphiques
        $chartData = [
            'attendance'   => $this->getAttendanceChartData($currentTerm),
            'payments'     => $this->getPaymentsChartData(),
            'grades'       => $this->getGradesChartData($currentTerm),
            'enrollments'  => $this->getEnrollmentsChartData(),
        ];

        // Top classes par moyenne
        $topClasses = $this->getTopClasses($currentTerm);

        // Top étudiants
        $topStudents = $this->getTopStudents($currentTerm);

        // Récapitulatif financier
        $financialSummary = $this->getFinancialSummary();

        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $terms         = Term::with('academicYear')->orderByDesc('start_date')->get();
        $classes       = Classe::orderBy('name')->get();

        return view('reports.index', compact(
            'stats',
            'chartData',
            'topClasses',
            'topStudents',
            'financialSummary',
            'currentYear',
            'currentTerm',
            'academicYears',
            'terms',
            'classes'
        ));
    }

    /**
     * Statistiques globales.
     */
    private function getGlobalStats(?AcademicYear $year): array
    {
        return [
            'total_students'  => Student::count(),
            'total_teachers'  => Teacher::where('status', 'active')->count(),
            'total_classes'   => Classe::count(),
            'total_subjects'  => Subject::where('is_active', true)->count(),
            'total_revenue'   => Payment::where('status', 'paid')->sum('amount'),
            'pending_payments'=> Payment::where('status', 'pending')->sum('amount'),
            'attendance_rate' => $this->getGlobalAttendanceRate(),
            'avg_grade'       => round(Grade::avg('score') ?? 0, 2),
        ];
    }

    /**
     * Taux de présence global.
     */
    private function getGlobalAttendanceRate(): float
    {
        $total   = Attendance::count();
        $present = Attendance::where('status', 'present')->count();

        return $total > 0 ? round(($present / $total) * 100, 1) : 0.0;
    }

    /**
     * Données graphique présences (7 derniers jours).
     */
    private function getAttendanceChartData(?Term $term): array
    {
        $days = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));

        return [
            'labels'  => $days->map(fn($d) => \Carbon\Carbon::parse($d)->translatedFormat('D d/m'))->toArray(),
            'present' => $days->map(fn($d) =>
                Attendance::whereDate('date', $d)->where('status', 'present')->count()
            )->toArray(),
            'absent'  => $days->map(fn($d) =>
                Attendance::whereDate('date', $d)->where('status', 'absent')->count()
            )->toArray(),
            'late'    => $days->map(fn($d) =>
                Attendance::whereDate('date', $d)->where('status', 'late')->count()
            )->toArray(),
        ];
    }

    /**
     * Données graphique paiements (6 derniers mois).
     */
    private function getPaymentsChartData(): array
    {
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i));

        return [
            'labels'  => $months->map(fn($m) => $m->translatedFormat('M Y'))->toArray(),
            'amounts' => $months->map(fn($m) =>
                Payment::where('status', 'paid')
                    ->whereYear('paid_at', $m->year)
                    ->whereMonth('paid_at', $m->month)
                    ->sum('amount')
            )->toArray(),
        ];
    }

    /**
     * Données graphique notes par matière.
     */
    private function getGradesChartData(?Term $term): array
    {
        $query = Grade::query()
            ->with('subject')
            ->selectRaw('subject_id, AVG(score) as avg_score')
            ->groupBy('subject_id');

        if ($term) {
            $query->where('term_id', $term->id);
        }

        $data = $query->get();

        return [
            'labels'  => $data->map(fn($g) => $g->subject?->name ?? 'Inconnue')->toArray(),
            'scores'  => $data->map(fn($g) => round($g->avg_score, 2))->toArray(),
        ];
    }

    /**
     * Données inscriptions par classe.
     */
    private function getEnrollmentsChartData(): array
    {
        $classes = Classe::withCount('students')->orderBy('name')->get();

        return [
            'labels' => $classes->pluck('name')->toArray(),
            'counts' => $classes->pluck('students_count')->toArray(),
        ];
    }

    /**
     * Top classes par moyenne générale.
     */
    private function getTopClasses(?Term $term): \Illuminate\Support\Collection
    {
        $query = Grade::query()
            ->with('classe')
            ->selectRaw('school_class_id, AVG(score) as avg_score, COUNT(*) as total_grades')
            ->groupBy('school_class_id')
            ->orderByDesc('avg_score')
            ->limit(5);

        if ($term) {
            $query->where('term_id', $term->id);
        }

        return $query->get();
    }

    /**
     * Top étudiants par moyenne.
     */
    private function getTopStudents(?Term $term): \Illuminate\Support\Collection
    {
        $query = Grade::query()
            ->with('student.user')
            ->selectRaw('student_id, AVG(score) as avg_score, COUNT(*) as total_grades')
            ->groupBy('student_id')
            ->orderByDesc('avg_score')
            ->limit(10);

        if ($term) {
            $query->where('term_id', $term->id);
        }

        return $query->get();
    }

    /**
     * Récapitulatif financier.
     */
    private function getFinancialSummary(): array
    {
        return [
            'total_paid'      => Payment::where('status', 'paid')->sum('amount'),
            'total_pending'   => Payment::where('status', 'pending')->sum('amount'),
            'total_overdue'   => Payment::where('status', 'overdue')->sum('amount'),
            'total_cancelled' => Payment::where('status', 'cancelled')->sum('amount'),
            'by_method'       => Payment::where('status', 'paid')
                                    ->selectRaw('payment_method, SUM(amount) as total')
                                    ->groupBy('payment_method')
                                    ->get()
                                    ->keyBy('payment_method'),
        ];
    }
}