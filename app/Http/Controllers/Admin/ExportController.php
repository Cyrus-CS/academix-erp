<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Http\Request;

class ExportController extends Controller
{
    // Centralise tous les exports Laravel Excel : étudiants, notes, présences, paiements.
     // ──────────────────────────────────────────────────────────────
    //  EXPORT STUDENTS EXCEL
    // ──────────────────────────────────────────────────────────────
    public function students(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('view students');

        $filename = 'eleves_' . now()->format('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StudentsExport(
                classId       : $request->integer('class_id') ?: null,
                gender        : $request->string('gender')->toString() ?: null,
                academicYearId: $request->integer('academic_year_id') ?: null,
                search        : $request->string('search')->toString() ?: null,
            ),
            $filename
        );
    }
}