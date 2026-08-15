<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Classe;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    private const MAX_PER_CATEGORY = 4;
    private const MIN_QUERY_LENGTH = 2;

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:' . self::MIN_QUERY_LENGTH, 'max:100'],
        ]);

        // Trim uniquement, pas de lower() ici → LIKE est case-insensitive sur MySQL/UTF8
        $q = trim($request->input('q'));

        // Debug temporaire — retire en production
        \Log::info('Search query: ' . $q);

        $results = collect([
            ...$this->searchStudents($q),
            ...$this->searchTeachers($q),
            ...$this->searchClasses($q),
            ...$this->searchSubjects($q),
            ...$this->searchPayments($q),
            ...$this->searchAnnouncements($q),
        ]);

        \Log::info('Search results count: ' . $results->count());

        return response()->json([
            'results' => $results->values(),
            'total'   => $results->count(),
            'query'   => $q,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Élèves
    |--------------------------------------------------------------------------
    */
    private function searchStudents(string $q): array
    {
        if (! auth()->user()->hasAnyRole(['Admin', 'Teacher'])) {
            return [];
        }

        try {
            return Student::query()
                ->with('user')
                ->where(function ($query) use ($q) {
                    // Recherche sur le nom de l'utilisateur lié
                    $query->whereHas('user', function ($userQuery) use ($q) {
                        $userQuery->where('name', 'LIKE', "%{$q}%");
                    })
                    // OU sur le matricule de l'étudiant
                    ->orWhere('matricule', 'LIKE', "%{$q}%");
                })
                ->limit(self::MAX_PER_CATEGORY)
                ->get()
                ->map(fn(Student $student) => [
                    'type'     => 'Élève',
                    'label'    => $student->user?->name ?? '—',
                    'sublabel' => $student->matricule ?? 'Aucun matricule',
                    'icon'     => 'bi-person-fill',
                    'color'    => 'blue',
                    'url'      => route('students.show', $student),
                ])
                ->toArray();

        } catch (\Exception $e) {
            \Log::error('Search students error: ' . $e->getMessage());
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Enseignants
    |--------------------------------------------------------------------------
    */
    private function searchTeachers(string $q): array
    {
        if (! auth()->user()->hasRole('Admin')) {
            return [];
        }

        try {
            return Teacher::query()
                ->with('user')
                ->where(function ($query) use ($q) {
                    $query->whereHas('user', function ($userQuery) use ($q) {
                        $userQuery->where('name', 'LIKE', "%{$q}%");
                    })
                    ->orWhere('employee_number', 'LIKE', "%{$q}%");
                })
                ->limit(self::MAX_PER_CATEGORY)
                ->get()
                ->map(fn(Teacher $teacher) => [
                    'type'     => 'Enseignant',
                    'label'    => $teacher->user?->name ?? '—',
                    'sublabel' => $teacher->employee_number ?? 'Aucun matricule',
                    'icon'     => 'bi-person-badge-fill',
                    'color'    => 'emerald',
                    'url'      => route('teachers.show', $teacher),
                ])
                ->toArray();

        } catch (\Exception $e) {
            \Log::error('Search teachers error: ' . $e->getMessage());
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Classes
    |--------------------------------------------------------------------------
    */
    private function searchClasses(string $q): array
    {
        try {
            return Classe::query()
                ->where(function ($query) use ($q) {
                    $query->where('name', 'LIKE', "%{$q}%")
                          ->orWhere('section', 'LIKE', "%{$q}%");
                })
                ->limit(self::MAX_PER_CATEGORY)
                ->get()
                ->map(fn(Classe $class) => [
                    'type'     => 'Classe',
                    'label'    => $class->name,
                    'sublabel' => $class->section ?? '',
                    'icon'     => 'bi-collection-fill',
                    'color'    => 'indigo',
                    'url'      => route('classes.show', $class),
                ])
                ->toArray();

        } catch (\Exception $e) {
            \Log::error('Search classes error: ' . $e->getMessage());
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Matières
    |--------------------------------------------------------------------------
    */
    private function searchSubjects(string $q): array
    {
        try {
            return Subject::query()
                ->where(function ($query) use ($q) {
                    $query->where('name', 'LIKE', "%{$q}%")
                          ->orWhere('code', 'LIKE', "%{$q}%");
                })
                ->limit(self::MAX_PER_CATEGORY)
                ->get()
                ->map(fn(Subject $subject) => [
                    'type'     => 'Matière',
                    'label'    => $subject->name,
                    'sublabel' => $subject->code ?? '',
                    'icon'     => 'bi-journal-bookmark-fill',
                    'color'    => 'cyan',
                    'url'      => route('subjects.show', $subject),
                ])
                ->toArray();

        } catch (\Exception $e) {
            \Log::error('Search subjects error: ' . $e->getMessage());
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Paiements
    |--------------------------------------------------------------------------
    */
    private function searchPayments(string $q): array
    {
        if (! auth()->user()->hasRole('Admin')) {
            return [];
        }

        try {
            return Payment::query()
                ->with('student.user')
                ->where(function ($query) use ($q) {
                    $query->where('transaction_reference', 'LIKE', "%{$q}%")
                          ->orWhereHas('student.user', function ($userQuery) use ($q) {
                              $userQuery->where('name', 'LIKE', "%{$q}%");
                          });
                })
                ->limit(self::MAX_PER_CATEGORY)
                ->get()
                ->map(fn(Payment $payment) => [
                    'type'     => 'Paiement',
                    'label'    => $payment->transaction_reference ?? '—',
                    'sublabel' => $payment->student?->user?->name ?? 'Élève inconnu',
                    'icon'     => 'bi-cash-stack',
                    'color'    => 'amber',
                    'url'      => route('payments.show', $payment),
                ])
                ->toArray();

        } catch (\Exception $e) {
            \Log::error('Search payments error: ' . $e->getMessage());
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Annonces
    |--------------------------------------------------------------------------
    */
    private function searchAnnouncements(string $q): array
    {
        try {
            return Announcement::query()
                ->where(function ($query) use ($q) {
                    $query->where('title', 'LIKE', "%{$q}%")
                          ->orWhere('content', 'LIKE', "%{$q}%");
                })
                ->limit(self::MAX_PER_CATEGORY)
                ->get()
                ->map(fn(Announcement $announcement) => [
                    'type'     => 'Annonce',
                    'label'    => $announcement->title,
                    'sublabel' => Str::limit($announcement->content ?? '', 50),
                    'icon'     => 'bi-megaphone-fill',
                    'color'    => 'red',
                    'url'      => route('announcements.show', $announcement),
                ])
                ->toArray();

        } catch (\Exception $e) {
            \Log::error('Search announcements error: ' . $e->getMessage());
            return [];
        }
    }
}