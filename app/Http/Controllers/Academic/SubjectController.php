<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectRequest;
use App\Models\Classe;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
// use Illuminate\Http\Request;

class SubjectController extends Controller
{
     /**
     * Display a listing of subjects.
     */
    public function index(Request $request): View
    {
    
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);
    
        $query = Subject::query()
            ->withCount(['grades', 'teacherAssignments'])
            ->latest();

        // Recherche
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $subjects = $query->paginate(15)->withQueryString();

        return view('subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create(): View
    {
        $subject = new Subject();
        $classes = Classe::orderBy('name')->get();

        return view('subjects.form', compact('subject', 'classes'));
    }

    /**
     * Store a newly created subject.
     */
    public function store(SubjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['code']      = Str::upper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Subject::create($validated);

        return redirect()
            ->route('subjects.index')
            ->with('success', "La matière « {$validated['name']} » a été créée avec succès.");
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject): View
    {
        $subject->load([
            'teacherAssignments.teacher.user',
            'teacherAssignments.schoolClass',
            'grades' => fn($q) => $q->latest()->limit(10),
        ]);

        return view('subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject): View
    {
        $classes = Classe::orderBy('name')->get();

        return view('subjects.form', compact('subject', 'classes'));
    }

    /**
     * Update the specified subject.
     */
    public function update(SubjectRequest $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validated();

        $validated['code']  = Str::upper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        $subject->update($validated);

        return redirect()
            ->route('subjects.index')
            ->with('success', "La matière « {$subject->name} » a été mise à jour avec succès.");
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        // Vérifier si la matière est utilisée
        if ($subject->grades()->exists()) {
            return redirect()
                ->route('subjects.index')
                ->with('error', "Impossible de supprimer « {$subject->name} » : des notes y sont associées.");
        }

        if ($subject->teacherAssignments()->exists()) {
            return redirect()
                ->route('subjects.index')
                ->with('error', "Impossible de supprimer « {$subject->name} » : des assignations d'enseignants y sont liées.");
        }

        $name = $subject->name;
        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('success', "La matière « {$name} » a été supprimée avec succès.");
    }
}