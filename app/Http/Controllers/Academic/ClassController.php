<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClasseRequest;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        $classes    = Classe::with(['students', 'teachers', 'subjects'])->paginate(9)->withQueryString();
        $activeYear = AcademicYear::active()->first();

        return view('classes.index', [
            'classes'       => $classes,
            'activeYear'    => $activeYear,
            'totalClasses'  => Classe::count(),
            'totalStudents' => Student::count(),
            'avgOccupancy'  => Classe::all()->avg(fn($c) => $c->occupancyRate()),
        ]);
    }

    public function create(): View
    {
        $classe = new Classe();
        $classe->fill([
            'name'     => 'Licence 3',
            'level'    => 'Licence Professionnelle',
            'capacity' => 50,
        ]);

        return view('classes.form', compact('classe'));
    }

    public function store(ClasseRequest $request): RedirectResponse
    {
        $classe = Classe::create($request->validated());

        return to_route('classes.index')
            ->with('success', "La classe « {$classe->name} » a été créée avec succès.");
    }

    public function show(Classe $classe): View
    {
        $classe->load(['students.user', 'teachers.user', 'subjects']);

        return view('classes.show', compact('classe'));
    }

    public function edit(Classe $classe): View
    {
        return view('classes.form', compact('classe'));
    }

    public function update(ClasseRequest $request, Classe $classe): RedirectResponse
    {
        $classe->update($request->validated());

        return to_route('classes.index')
            ->with('success', "La classe « {$classe->name} » a été modifiée avec succès.");
    }

    public function destroy(Classe $classe): RedirectResponse
    {
        if ($classe->students()->exists()) {
            return to_route('classes.index')
                ->with('error', "Impossible de supprimer « {$classe->name} » : des étudiants y sont inscrits.");
        }

        $name = $classe->name;
        $classe->delete();

        return to_route('classes.index')
            ->with('success', "La classe « {$name} » a été supprimée avec succès.");
    }

    /**
     * Réordonner les classes via SortableJS (AJAX).
     */
    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer', 'exists:classes,id'],
        ]);

        foreach ($request->input('order') as $position => $id) {
            Classe::where('id', $id)->update(['position' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }

}