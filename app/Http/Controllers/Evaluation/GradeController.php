<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Grade\GradeRequest;
use App\Models\Classe;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GradeController extends Controller
{
    /**
     * Display a listing of grades.
     */
    public function index(Request $request): View
    {
        $user    = Auth::user();
        $query   = Grade::query()
            ->with([
                'student.user',
                'subject',
                'term.academicYear',
                'teacher.user',
                'classe',
            ])
            ->latest();

        // Si enseignant, limiter à ses matières/classes
        if ($user->hasRole('Teacher')) {
            $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
            $query->where('teacher_id', $teacher->id);
        }

        // Filtres
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }

        if ($request->filled('term_id')) {
            $query->where('term_id', $request->input('term_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        $grades   = $query->paginate(20)->withQueryString();
        $classes  = Classe::orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $terms    = Term::with('academicYear')->orderByDesc('start_date')->get();
        $students = Student::with('user')->orderBy('matricule')->get();

        return view('grades.index', compact(
            'grades',
            'classes',
            'subjects',
            'terms',
            'students'
        ));
    }

    /**
     * Show the form for creating a new grade.
     */
    public function create(): View
    {
        $grade    = new Grade();
        $user     = Auth::user();
        $students = Student::with('user')->orderBy('matricule')->get();
        $terms    = Term::with('academicYear')->orderByDesc('start_date')->get();
        $classes  = Classe::orderBy('name')->get();
        $currentTerm = Term::where('is_current', true)->first();

        // Si enseignant : filtrer ses matières
        // Récupérer le teacher_id selon le rôle
        if ($user->hasRole('Teacher')) {
            $teacher  = Teacher::where('user_id', $user->id)->firstOrFail();
            $subjects = Subject::whereHas('assignments', fn($q) =>
                $q->where('teacher_id', $teacher->id)
            )->get();
            $teachers = collect(); // pas utilisé côté vue si $teacher existe
        } else {
            $subjects = Subject::where('is_active', true)->orderBy('name')->get();
            $teacher  = null;
            $teachers = Teacher::with('user')->orderBy('id')->get(); 
        }

        return view('grades.form', compact(
            'grade',
            'students',
            'subjects',
            'terms',
            'classes',
            'currentTerm',
            'teacher',
            'teachers'
        ));
    }

    /**
     * Store a newly created grade.
     */
    public function store(GradeRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validated();

        // Vérifier l'unicité (même étudiant, même matière, même trimestre, même type)
        $exists = Grade::where([
            'student_id' => $validated['student_id'],
            'subject_id' => $validated['subject_id'],
            'term_id'    => $validated['term_id'],
            'type'       => $validated['type'],
        ])->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('warning', 'Une note de ce type existe déjà pour cet étudiant dans cette matière ce trimestre.');
        }

        // Déduire l'année académique depuis le trimestre sélectionné
        $term = Term::findOrFail($validated['term_id']);
        $validated['academic_year_id'] = $term->academic_year_id;

        Grade::create($validated);

        return redirect()
            ->route('grades.index')
            ->with('success', 'La note a été enregistrée avec succès.');
    }

    /**
     * Display the specified grade.
     */
    public function show(Grade $grade): View
    {
        $this->authorizeGradeAccess($grade);

        $grade->load([
            'student.user',
            'subject',
            'term.academicYear',
            'teacher.user',
            'classe',
        ]);

        return view('grades.show', compact('grade'));
    }

    /**
     * Show the form for editing the specified grade.
     */
    public function edit(Grade $grade): View
    {
        $this->authorizeGradeAccess($grade);

        $user     = Auth::user();
        $students = Student::with('user')->orderBy('matricule')->get();
        $terms    = Term::with('academicYear')->orderByDesc('start_date')->get();
        $classes  = Classe::orderBy('name')->get();

         if ($user->hasRole('Teacher')) {
            $teacher  = Teacher::where('user_id', $user->id)->firstOrFail();
            $subjects = Subject::whereHas('assignments', fn($q) =>
                $q->where('teacher_id', $teacher->id)
            )->get();
            $teachers = collect(); // pas utilisé côté vue si $teacher existe
        } else {
            $subjects = Subject::where('is_active', true)->orderBy('name')->get();
            $teacher  = null;
            $teachers = Teacher::with('user')->orderBy('id')->get(); 
        }

        return view('grades.form', compact(
            'grade',
            'students',
            'subjects',
            'terms',
            'classes',
            'teacher',
            'teachers'
        ));
    }

    /**
     * Update the specified grade.
     */
    public function update(GradeRequest $request, Grade $grade): RedirectResponse
    {
        $this->authorizeGradeAccess($grade);
        $user = Auth::user();

        $validated = $request->validated();

        $grade->update($validated);

        return redirect()
            ->route('grades.index')
            ->with('success', 'La note a été mise à jour avec succès.');
    }

    /**
     * Remove the specified grade.
     */
    public function destroy(Grade $grade): RedirectResponse
    {
        $this->authorizeGradeAccess($grade);

        $grade->delete();

        return redirect()
            ->route('grades.index')
            ->with('success', 'La note a été supprimée avec succès.');
    }

    /**
     * Autoriser l'accès à une note selon le rôle.
     */
    private function authorizeGradeAccess(Grade $grade): void
    {
        $user = Auth::user();

        if ($user->hasRole('Teacher')) {
            $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
            if ($grade->teacher_id !== $teacher->id) {
                abort(403, 'Vous n\'avez pas accès à cette note.');
            }
        }
    }
}