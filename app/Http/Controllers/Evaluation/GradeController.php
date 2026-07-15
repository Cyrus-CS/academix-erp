<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
// use Illuminate\Http\Request;

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
                'schoolClass',
            ])
            ->latest();

        // Si enseignant, limiter à ses matières/classes
        if ($user->hasRole('Teacher')) {
            $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
            $query->where('teacher_id', $teacher->id);
        }

        // Filtres
        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->input('class_id'));
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
        $students = Student::with('user')->orderBy('student_number')->get();

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
        $students = Student::with('user')->orderBy('student_number')->get();
        $terms    = Term::with('academicYear')->orderByDesc('start_date')->get();
        $classes  = Classe::orderBy('name')->get();
        $currentTerm = Term::where('is_current', true)->first();

        // Si enseignant : filtrer ses matières
        if ($user->hasRole('Teacher')) {
            $teacher  = Teacher::where('user_id', $user->id)->firstOrFail();
            $subjects = Subject::whereHas('teacherAssignments', fn($q) =>
                $q->where('teacher_id', $teacher->id)
            )->get();
        } else {
            $subjects = Subject::where('is_active', true)->orderBy('name')->get();
            $teacher  = null;
        }

        return view('grades.form', compact(
            'grade',
            'students',
            'subjects',
            'terms',
            'classes',
            'currentTerm',
            'teacher'
        ));
    }

    /**
     * Store a newly created grade.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'student_id'      => ['required', 'exists:students,id'],
            'subject_id'      => ['required', 'exists:subjects,id'],
            'term_id'         => ['required', 'exists:terms,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'type'            => ['required', Rule::in(['homework', 'test', 'exam'])],
            'score'           => ['required', 'numeric', 'min:0', 'max:20'],
            'max_score'       => ['required', 'numeric', 'min:1', 'max:20'],
            'comment'         => ['nullable', 'string', 'max:500'],
            'graded_at'       => ['required', 'date'],
        ], [
            'student_id.required'      => "L'étudiant est obligatoire.",
            'subject_id.required'      => 'La matière est obligatoire.',
            'term_id.required'         => 'Le trimestre est obligatoire.',
            'school_class_id.required' => 'La classe est obligatoire.',
            'type.required'            => 'Le type de note est obligatoire.',
            'score.required'           => 'La note est obligatoire.',
            'score.min'                => 'La note minimum est 0.',
            'score.max'                => 'La note maximum est 20.',
            'graded_at.required'       => 'La date d\'évaluation est obligatoire.',
        ]);

        // Récupérer le teacher_id selon le rôle
        if ($user->hasRole('Teacher')) {
            $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
            $validated['teacher_id'] = $teacher->id;
        } else {
            $validated['teacher_id'] = $request->input('teacher_id');
        }

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
            'schoolClass',
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
        $students = Student::with('user')->orderBy('student_number')->get();
        $terms    = Term::with('academicYear')->orderByDesc('start_date')->get();
        $classes  = Classe::orderBy('name')->get();

        if ($user->hasRole('Teacher')) {
            $teacher  = Teacher::where('user_id', $user->id)->firstOrFail();
            $subjects = Subject::whereHas('teacherAssignments', fn($q) =>
                $q->where('teacher_id', $teacher->id)
            )->get();
        } else {
            $subjects = Subject::where('is_active', true)->orderBy('name')->get();
            $teacher  = null;
        }

        return view('grades.form', compact(
            'grade',
            'students',
            'subjects',
            'terms',
            'classes',
            'teacher'
        ));
    }

    /**
     * Update the specified grade.
     */
    public function update(Request $request, Grade $grade): RedirectResponse
    {
        $this->authorizeGradeAccess($grade);

        $validated = $request->validate([
            'student_id'      => ['required', 'exists:students,id'],
            'subject_id'      => ['required', 'exists:subjects,id'],
            'term_id'         => ['required', 'exists:terms,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'type'            => ['required', Rule::in(['homework', 'test', 'exam'])],
            'score'           => ['required', 'numeric', 'min:0', 'max:20'],
            'max_score'       => ['required', 'numeric', 'min:1', 'max:20'],
            'comment'         => ['nullable', 'string', 'max:500'],
            'graded_at'       => ['required', 'date'],
        ]);

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