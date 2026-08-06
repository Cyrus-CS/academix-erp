<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportRequest;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classe;
use App\Models\Grade;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ReportController extends Controller
{
    /** Durée de cache par défaut (secondes). */
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Display the reports dashboard.
     */
    public function index(ReportRequest $request): View
    {
        // ── Validation des filtres (sécurité + intégrité) ──────────────
        $validated = $request->validated();

        // ── Résolution des filtres actifs (fallback sur l'actuel) ──────
        $currentYear = $validated['academic_year_id'] ?? null
            ? AcademicYear::find($validated['academic_year_id'])
            : AcademicYear::where('is_current', true)->first();

        $currentTerm = $validated['term_id'] ?? null
            ? Term::find($validated['term_id'])
            : Term::where('is_current', true)->first();

        $classId = $validated['class_id'] ?? null;

        // ── Clé de cache basée sur les filtres actifs ───────────────────
        $cacheKey = sprintf(
            'reports.year-%s.term-%s.class-%s',
            $currentYear?->id ?? 'none',
            $currentTerm?->id ?? 'none',
            $classId ?? 'all'
        );

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($currentYear, $currentTerm, $classId) {
            return [
                'stats'  => $this->getGlobalStats(),
                'chartData'   => [
                    'attendance'  => $this->getAttendanceChartData(),
                    'payments'    => $this->getPaymentsChartData(),
                    'grades'      => $this->getGradesChartData($currentTerm),
                    'enrollments' => $this->getEnrollmentsChartData(),
                ],
                'topClasses'  => $this->getTopClasses($currentTerm),
                'topStudents' => $this->getTopStudents($currentTerm, $classId),
                'financialSummary'  => $this->getFinancialSummary(),
            ];
        });

        // ── Données des filtres (légères, pas besoin de cache) ──────────
        $academicYears = AcademicYear::orderByDesc('start_date')->get(['id', 'name', 'start_date', 'is_current']);
        $terms         = Term::with('academicYear:id,name')
            ->orderByDesc('start_date')
            ->get(['id', 'name', 'academic_year_id', 'start_date', 'is_current']);
        $classes       = Classe::orderBy('name')->get(['id', 'name']);

        return view('reports.index', [
            'stats'             => $data['stats'],
            'chartData'         => $data['chartData'],
            'topClasses'        => $data['topClasses'],
            'topStudents'       => $data['topStudents'],
            'financialSummary'  => $data['financialSummary'],
            'currentYear'       => $currentYear,
            'currentTerm'       => $currentTerm,
            'currentClassId'    => $classId,
            'academicYears'     => $academicYears,
            'terms'             => $terms,
            'classes'           => $classes,
        ]);
    }

    /**
     * Statistiques globales — fusionnées en un minimum de requêtes.
     */
    private function getGlobalStats(): array
    {
        return [
            'total_students'   => Student::count(),
            'total_teachers'   => Teacher::where('status', 'active')->count(),
            'total_classes'    => Classe::count(),
            'total_subjects'   => Subject::where('is_active', true)->count(),
            'total_revenue'    => Payment::where('status', 'paid')->sum('amount_paid'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'attendance_rate'  => $this->getGlobalAttendanceRate(),
            'avg_grade'        => round(Grade::avg('score') ?? 0, 2),
        ];
    }

    /**p(Post
     * Taux de présence global — 1 seule requête (au lieu de 2).
     */
    private function getGlobalAttendanceRate(): float
    {
        $stats = Attendance::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
            ")
            ->first();

        return $stats->total > 0
            ? round(($stats->present / $stats->total) * 100, 1)
            : 0.0;
    }

    /**
     * Données graphique présences (7 derniers jours) — 1 seule requête (au lieu de 21).
     */
    private function getAttendanceChartData(): array
    {
        $startDate = now()->subDays(6)->startOfDay();
        $endDate   = now()->endOfDay();

        $rows = Attendance::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('DATE(date) as day, status, COUNT(*) as total')
            ->groupBy('day', 'status')
            ->get()
            ->groupBy('day');

        $days = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));

        $present = [];
        $absent  = [];
        $late    = [];

        foreach ($days as $day) {
            $dayRows = $rows->get($day, collect());
            $present[] = (int) optional($dayRows->firstWhere('status', 'present'))->total;
            $absent[]  = (int) optional($dayRows->firstWhere('status', 'absent'))->total;
            $late[]    = (int) optional($dayRows->firstWhere('status', 'late'))->total;
        }

        return [
            'labels'  => $days->map(fn($d) => Carbon::parse($d)->translatedFormat('D d/m'))->toArray(),
            'present' => $present,
            'absent'  => $absent,
            'late'    => $late,
        ];
    }

    /**
     * Données graphique paiements (6 derniers mois) - 1 seule requête (au lieu de 6).
     */
    private function getPaymentsChartData(): array
    {
        $startDate = now()->subMonths(5)->startOfMonth();
        $endDate   = now()->endOfMonth();

        $rows = Payment::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as ym, SUM(amount_paid) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i));

        return [
            'labels'  => $months->map(fn($m) => $m->translatedFormat('M Y'))->toArray(),
            'amounts' => $months->map(fn($m) => (float) ($rows->get($m->format('Y-m')) ?? 0))->toArray(),
        ];
    }

    /**
     * Données graphique notes par matière.
     */
    private function getGradesChartData(?Term $term): array
    {
        $query = Grade::query()
            ->with('subject:id,name')
            ->selectRaw('subject_id, AVG(score) as avg_score')
            ->groupBy('subject_id');

        if ($term) {
            $query->where('term_id', $term->id);
        }

        $data = $query->get();

        return [
            'labels' => $data->map(fn($g) => $g->subject?->name ?? 'Inconnue')->toArray(),
            'scores' => $data->map(fn($g) => round((float) $g->avg_score, 2))->toArray(),
        ];
    }

    /**
     * Données inscriptions par classe.
     */
    private function getEnrollmentsChartData(): array
    {
        $classes = Classe::withCount('students')
            ->orderBy('name')
            ->get(['id', 'name']);

        return [
            'labels' => $classes->pluck('name')->toArray(),
            'counts' => $classes->pluck('students_count')->toArray(),
        ];
    }

    /**
     * Top classes par moyenne générale.
     */
    private function getTopClasses(?Term $term): Collection
    {
        $query = Grade::query()
            ->with('classe:id,name')
            ->selectRaw('class_id, AVG(score) as avg_score, COUNT(*) as total_grades')
            ->groupBy('class_id')
            ->orderByDesc('avg_score')
            ->limit(5);

        if ($term) {
            $query->where('term_id', $term->id);
        }

        return $query->get();
    }

    /**
     * Top étudiants par moyenne (avec filtre optionnel par classe).
     */
    private function getTopStudents(?Term $term, ?int $classId = null): Collection
    {
        $query = Grade::query()
            ->with(['student:id,user_id,class_id', 'student.user:id,name,avatar'])
            ->selectRaw('student_id, AVG(score) as avg_score, COUNT(*) as total_grades')
            ->groupBy('student_id')
            ->orderByDesc('avg_score')
            ->limit(10);

        if ($term) {
            $query->where('term_id', $term->id);
        }

        if ($classId) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $classId));
        }

        return $query->get();
    }

    /**
     * Récapitulatif financier - 1 seule requête pour tous les statuts (au lieu de 4).
     */
    private function getFinancialSummary(): array
    {
        $byStatus = Payment::selectRaw('status, SUM(amount_paid) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byMethod = Payment::where('status', 'paid')
            ->selectRaw('payment_method, SUM(amount_paid) as total')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        return [
            'total_paid'      => (float) ($byStatus['paid'] ?? 0),
            'total_pending'   => (float) ($byStatus['pending'] ?? 0),
            'total_overdue'   => (float) ($byStatus['overdue'] ?? 0),
            'total_cancelled' => (float) ($byStatus['cancelled'] ?? 0),
            'by_method'       => $byMethod,
        ];
    }
}