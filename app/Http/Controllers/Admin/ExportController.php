<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceExport;
use App\Exports\GradesExport;
use App\Exports\PaymentsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{

    /**
     * Page principale des exports.
     */
    public function index(): View
    {
        $exports = [
            [
                'key'         => 'students',
                'label'       => 'Étudiants',
                'description' => 'Liste complète des étudiants avec classe et informations personnelles.',
                'icon'        => 'bi-mortarboard-fill',
                'color'       => 'blue',
                'route'       => route('exports.students'),
                'filename'    => 'etudiants_' . now()->format('Y-m-d') . '.xlsx',
            ],
            [
                'key'         => 'grades',
                'label'       => 'Notes',
                'description' => 'Toutes les notes par étudiant, matière et trimestre.',
                'icon'        => 'bi-journal-bookmark-fill',
                'color'       => 'emerald',
                'route'       => route('exports.grades'),
                'filename'    => 'notes_' . now()->format('Y-m-d') . '.xlsx',
            ],
            [
                'key'         => 'attendance',
                'label'       => 'Présences',
                'description' => 'Historique des présences, absences et retards.',
                'icon'        => 'bi-calendar-check-fill',
                'color'       => 'amber',
                'route'       => route('exports.attendance'),
                'filename'    => 'presences_' . now()->format('Y-m-d') . '.xlsx',
            ],
            [
                'key'         => 'payments',
                'label'       => 'Paiements',
                'description' => 'Historique complet des paiements et statuts financiers.',
                'icon'        => 'bi-cash-stack',
                'color'       => 'cyan',
                'route'       => route('exports.payments'),
                'filename'    => 'paiements_' . now()->format('Y-m-d') . '.xlsx',
            ],
        ];

        return view('exports.index', compact('exports'));
    }

    // ──────────────────────────────────────────────────────────────
    //  EXPORT STUDENTS EXCEL
    // ──────────────────────────────────────────────────────────────
    public function students(Request $request): BinaryFileResponse
    {
        $this->authorize('view students');

        $filename = 'eleves_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\StudentsExport(
                classId       : $request->integer('class_id') ?: null,
                gender        : $request->string('gender')->toString() ?: null,
                academicYearId: $request->integer('academic_year_id') ?: null,
                search        : $request->string('search')->toString() ?: null,
            ),
            $filename
        );
    }

    /**
     * Export des notes.
     */
    public function grades(): BinaryFileResponse
    {
        return Excel::download(
            new GradesExport(),
            'notes_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export des présences.
     */
    public function attendance(): BinaryFileResponse
    {
        return Excel::download(
            new AttendanceExport(),
            'presences_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export des paiements.
     */
    public function payments(): BinaryFileResponse
    {
        return Excel::download(
            new PaymentsExport(),
            'paiements_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}