<?php

namespace App\Services\Dashboard;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Classe;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherContract;
use App\Models\User;
use App\Support\Concerns\ResolvesAcademicPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminDashboardService
{
    use ResolvesAcademicPeriod;

    /**
     * Durée du cache en minutes pour les statistiques lourdes.
     */
    private const CACHE_TTL = 10;

    private const CACHE_KEYS = [
        'dashboard.admin.stats',
        'dashboard.admin.attendance_chart',
        'dashboard.admin.revenue_chart',
        'dashboard.admin.students_by_class',
        'dashboard.admin.top_absents',
    ];

    public function build(): array
    {
        $activeYear = $this->activeAcademicYear();

        return [
            'stats' => $this->stats(),
            'attendanceChart' => $this->attendanceChart(),
            'revenueChart' => $this->revenueChart(),
            'studentsByClass' => $this->studentsByClass(),
            'recentPayments' => $this->recentPayments(),
            'announcements' => $this->announcements(),
            'topAbsentStudents' => $this->topAbsentStudents(),
            'expiringContracts' => $this->expiringContracts(),
            'activeYear' => $activeYear,
            'currentTerm' => $this->currentTerm($activeYear?->id),
        ];
    }

    /**
     * Vide tout le cache du dashboard admin (utilisé par le rafraîchissement AJAX).
     */
    public static function clearCache(): void
    {
        foreach (self::CACHE_KEYS as $key) {
            Cache::forget($key);
        }
    }

    private function stats(): array
    {
        return Cache::remember('dashboard.admin.stats', self::CACHE_TTL * 60, function () {
            return [
                // Nombres total de classe, etudiants, utilisateurs et enseignants
                'total_classes' => Classe::count(),
                'total_students' => Student::count(),
                'total_users' => User::count(),
                'total_teachers' => Teacher::count(),

                // Nombre de nouveaux professeurs et etudiants dans le mois actuel
                'new_students_in_month' => Student::newStudentsInMonth(),
                'new_teachers_in_month' => Teacher::newTeachersInMonth(),

                // Nombre de presents, absents, retard du jour
                'attendances_today' => [
                    'present' => Attendance::present(),
                    'absent' => Attendance::absent(),
                    'late' => Attendance::late(),
                ],

                // Nombre de payments du mois
                'payments_month' => Payment::paymentsMonth(),
                'payments_today' => Payment::paymentToday(),
                'payments_pending' => Payment::pending()->count(),
                'payments_paid' => Payment::paid()->count(),
                'active_contracts_teachers' => TeacherContract::active()->count(),
            ];
        });
    }

    /**
     * Graphique : présences des 7 derniers jours.
     */
    private function attendanceChart(): array
    {
        return Cache::remember('dashboard.admin.attendance_chart', self::CACHE_TTL * 60, function () {
            $days = collect(range(6, 0))->map(function ($daysAgo) {
                $date = Carbon::today()->subDays($daysAgo);
                $records = Attendance::whereDate('date', $date)->get();

                return [
                    'date' => $date->translatedFormat('D d/m'),
                    'present' => $records->where('status', 'present')->count(),
                    'absent' => $records->where('status', 'absent')->count(),
                    'late' => $records->where('status', 'late')->count(),
                ];
            });

            return [
                'labels' => $days->pluck('date')->toArray(),
                'present' => $days->pluck('present')->toArray(),
                'absent' => $days->pluck('absent')->toArray(),
                'late' => $days->pluck('late')->toArray(),
            ];
        });
    }

    /**
     * Graphique : revenus des 6 derniers mois.
     */
    private function revenueChart(): array
    {
        return Cache::remember('dashboard.admin.revenue_chart', self::CACHE_TTL * 60, function () {
            $months = collect(range(5, 0))->map(function ($monthsAgo) {
                $date = Carbon::now()->subMonths($monthsAgo);

                return [
                    'month' => $date->translatedFormat('M Y'),
                    'amount' => Payment::whereMonth('paid_at', $date->month)
                        ->whereYear('paid_at', $date->year)
                        ->where('status', 'paid')
                        ->sum('amount_paid'),
                ];
            });

            return [
                'labels' => $months->pluck('month')->toArray(),
                'amounts' => $months->pluck('amount')->toArray(),
            ];
        });
    }

    /**
     * Répartition des élèves par classe (top 8).
     */
    private function studentsByClass()
    {
        return Cache::remember('dashboard.admin.students_by_class', self::CACHE_TTL * 60, function () {
            return Classe::withCount('students')
                ->orderByDesc('students_count')
                ->take(8)
                ->get()
                ->map(fn ($class) => [
                    'name' => $class->name,
                    'count' => $class->students_count,
                ]);
        });
    }

    private function recentPayments()
    {
        return Payment::with(['student.user', 'feeType'])
            ->latest('paid_at')
            ->take(5)
            ->get();
    }

    private function announcements()
    {
        return Announcement::with('user')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->latest()
            ->take(4)
            ->get();
    }

    /**
     * Top 5 des élèves les plus absents du mois en cours.
     */
    private function topAbsentStudents()
    {
        return Cache::remember('dashboard.admin.top_absents', self::CACHE_TTL * 60, function () {
            return Student::with('user')
                ->withCount([
                    'attendances as absences_count' => fn ($q) => $q
                        ->where('status', 'absent')
                        ->whereMonth('date', now()->month),
                ])
                ->orderByDesc('absences_count')
                ->take(5)
                ->get()
                ->filter(fn ($s) => $s->absences_count > 0);
        });
    }

    /**
     * Contrats enseignants actifs expirant dans les 30 prochains jours.
     */
    private function expiringContracts()
    {
        return TeacherContract::with('teacher.user')
            ->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->orderBy('end_date')
            ->take(4)
            ->get();
    }
}