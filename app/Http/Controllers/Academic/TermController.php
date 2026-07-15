<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TermController extends Controller
{
    /**
     * Display a listing of terms.
     */
    public function index(Request $request): View
    {
        $query = Term::query()
            ->with('academicYear')
            ->withCount(['grades', 'reportCards'])
            ->latest();

        // Filtre par année académique
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->input('academic_year_id'));
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('is_current', $request->input('status') === 'current');
        }

        $terms         = $query->paginate(15)->withQueryString();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('terms.index', compact('terms', 'academicYears'));
    }

    /**
     * Show the form for creating a new term.
     */
    public function create(): View
    {
        $term = new Term();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('terms.form', compact('term', 'academicYears'));
    }

    /**
     * Store a newly created term.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name'             => ['required', 'string', 'max:100'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after:start_date'],
            'is_current'       => ['boolean'],
        ], [
            'academic_year_id.required' => "L'année académique est obligatoire.",
            'academic_year_id.exists'   => "L'année académique sélectionnée est invalide.",
            'name.required'             => 'Le nom du trimestre est obligatoire.',
            'start_date.required'       => 'La date de début est obligatoire.',
            'end_date.required'         => 'La date de fin est obligatoire.',
            'end_date.after'            => 'La date de fin doit être postérieure à la date de début.',
        ]);

        $validated['is_current'] = $request->boolean('is_current');

        // Si ce trimestre est défini comme actif, désactiver les autres
        if ($validated['is_current']) {
            Term::where('academic_year_id', $validated['academic_year_id'])
                ->update(['is_current' => false]);
        }

        $term = Term::create($validated);

        return redirect()
            ->route('terms.index')
            ->with('success', "Le trimestre « {$term->name} » a été créé avec succès.");
    }

    /**
     * Display the specified term.
     */
    public function show(Term $term): View
    {
        $term->load([
            'academicYear',
            'grades.student.user',
            'grades.subject',
            'reportCards' => fn($q) => $q->with('student.user')->latest()->limit(10),
        ]);

        $stats = [
            'total_grades'       => $term->grades()->count(),
            'total_report_cards' => $term->reportCards()->count(),
            'avg_grade'          => round($term->grades()->avg('score') ?? 0, 2),
        ];

        return view('terms.show', compact('term', 'stats'));
    }

    /**
     * Show the form for editing the specified term.
     */
    public function edit(Term $term): View
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('terms.form', compact('term', 'academicYears'));
    }

    /**
     * Update the specified term.
     */
    public function update(Request $request, Term $term): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name'             => ['required', 'string', 'max:100'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after:start_date'],
            'is_current'       => ['boolean'],
        ], [
            'academic_year_id.required' => "L'année académique est obligatoire.",
            'end_date.after'            => 'La date de fin doit être postérieure à la date de début.',
        ]);

        $validated['is_current'] = $request->boolean('is_current');

        // Si ce trimestre devient actif, désactiver les autres de la même année
        if ($validated['is_current']) {
            Term::where('academic_year_id', $validated['academic_year_id'])
                ->where('id', '!=', $term->id)
                ->update(['is_current' => false]);
        }

        $term->update($validated);

        return redirect()
            ->route('terms.index')
            ->with('success', "Le trimestre « {$term->name} » a été mis à jour avec succès.");
    }

    /**
     * Remove the specified term.
     */
    public function destroy(Term $term): RedirectResponse
    {
        if ($term->grades()->exists()) {
            return redirect()
                ->route('terms.index')
                ->with('error', "Impossible de supprimer « {$term->name} » : des notes y sont associées.");
        }

        if ($term->reportCards()->exists()) {
            return redirect()
                ->route('terms.index')
                ->with('error', "Impossible de supprimer « {$term->name} » : des bulletins y sont liés.");
        }

        if ($term->is_current) {
            return redirect()
                ->route('terms.index')
                ->with('error', "Impossible de supprimer le trimestre actif.");
        }

        $name = $term->name;
        $term->delete();

        return redirect()
            ->route('terms.index')
            ->with('success', "Le trimestre « {$name} » a été supprimé avec succès.");
    }
}