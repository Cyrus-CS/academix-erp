<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicYearRequest;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $academicYears = AcademicYear::paginate(9)->withQueryString();
        $activeYear    = AcademicYear::active()->first();

        return view('academic-years.index', [
            'academicYears' => $academicYears,
            'activeYear'    => $activeYear,
            'totalYears'    => AcademicYear::count(),
            'totalTerms'    => Term::count(),
            'totalClasses'  => Classe::count(),
        ]);
    }

    public function create(): View
    {
        $academic = new AcademicYear();
        return view('academic-years.form', compact('academic'));
    }

    public function store(AcademicYearRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        AcademicYear::create([
            'name'       => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'is_current' => false,
        ]);

        return to_route('academic-years.index')
            ->with('success', "L'année académique a été créée avec succès.");
    }

    public function edit(AcademicYear $academic): View
    {
        return view('academic-years.form', compact('academic'));
    }

    public function update(AcademicYearRequest $request, AcademicYear $academic): RedirectResponse
    {
        $academic->update($request->validated());

        return to_route('academic-years.index')
            ->with('success', "L'année académique a été modifiée avec succès.");
    }

    public function destroy(AcademicYear $academic): RedirectResponse
    {
        // Vérifier les dépendances
        if ($academic->terms()->exists()) {
            return to_route('academic-years.index')
                ->with('error', "Impossible de supprimer : des trimestres sont liés à cette année.");
        }

        if ($academic->is_current) {
            return to_route('academic-years.index')
                ->with('error', "Impossible de supprimer l'année académique active.");
        }

        $name = $academic->name;
        $academic->delete();

        return to_route('academic-years.index')
            ->with('success', "L'année académique « {$name} » a été supprimée avec succès.");
    }

    // Activer une année
    public function activate(AcademicYear $academicYear): RedirectResponse
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
            $academicYear->update(['is_current' => true]);
        });

        return back()->with('success', "{$academicYear->name} est maintenant l'année active.");
    }

    // Réordonner
    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $order = $request->input('order', []);
        foreach ($order as $position => $id) {
            AcademicYear::where('id', $id)->update(['order' => $position + 1]);
        }
        return response()->json(['ok' => true]);
    }
    
}