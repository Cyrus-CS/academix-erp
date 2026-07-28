<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\ClassSubjectTeacher;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
// use Illuminate\Http\Request;

class TeacherAssignmentController extends Controller
{
    
    /**
     * Display a listing of teacher assignments.
     */
    public function index(Request $request): View
    {
        $query = ClassSubjectTeacher::query()
            ->with([
                'teacher.user',
                'subject',
                'classe',
                'academicYear',
            ])
            ->latest();

        // Filtre enseignant
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->input('teacher_id'));
        }

        // Filtre matière
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }

        // Filtre classe
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        // Filtre année académique
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->input('academic_year_id'));
        } else {
            // Par défaut : année courante
            $currentYear = AcademicYear::active()->first();
            if ($currentYear) {
                $query->where('academic_year_id', $currentYear->id);
            }
        }

        $assignments   = $query->paginate(15)->withQueryString();
        $teachers      = Teacher::with('user')->orderBy('employee_number')->get();
        $subjects      = Subject::where('is_active', true)->orderBy('name')->get();
        $classes       = Classe::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('teacher-assignments.index', compact(
            'assignments',
            'teachers',
            'subjects',
            'classes',
            'academicYears'
        ));
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create(): View
    {
        $assignment    = new ClassSubjectTeacher();
        $teachers      = Teacher::with('user')->orderBy('employee_number')->get();
        $subjects      = Subject::where('is_active', true)->orderBy('name')->get();
        $classes       = Classe::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $currentYear   = AcademicYear::active()->first();

        return view('teacher-assignments.form', compact(
            'assignment',
            'teachers',
            'subjects',
            'classes',
            'academicYears',
            'currentYear'
        ));
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id'       => ['required', 'exists:teachers,id'],
            'subject_id'       => ['required', 'exists:subjects,id'],
            'class_id'  => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ], [
            'teacher_id.required'       => "L'enseignant est obligatoire.",
            'subject_id.required'       => 'La matière est obligatoire.',
            'class_id.required'  => 'La classe est obligatoire.',
            'academic_year_id.required' => "L'année académique est obligatoire.",
        ]);

        // Vérifier l'unicité de l'assignation
        $exists = ClassSubjectTeacher::where($validated)->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Cette assignation existe déjà pour cette année académique.');
        }

        // Vérifier qu'un autre enseignant n'enseigne pas déjà cette matière dans cette classe
        $conflict = ClassSubjectTeacher::where([
            'subject_id'       => $validated['subject_id'],
            'class_id'  => $validated['class_id'],
            'academic_year_id' => $validated['academic_year_id'],
        ])->where('teacher_id', '!=', $validated['teacher_id'])->exists();

        if ($conflict) {
            return redirect()
                ->back()
                ->withInput()
                ->with('warning', 'Un autre enseignant est déjà assigné à cette matière dans cette classe.');
        }

        ClassSubjectTeacher::create($validated);

        return redirect()
            ->route('teacher-assignments.index')
            ->with('success', 'L\'assignation a été créée avec succès.');
    }

    /**
     * Display the specified assignment.
     */
    public function show(ClassSubjectTeacher $teacherAssignment): View
    {
        $teacherAssignment->load([
            'teacher.user',
            'subject',
            'classe.students.user',
            'academicYear',
        ]);

        return view('teacher-assignments.show', compact('teacherAssignment'));
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(ClassSubjectTeacher $teacherAssignment): View
    {
        $teachers      = Teacher::with('user')->orderBy('employee_number')->get();
        $subjects      = Subject::where('is_active', true)->orderBy('name')->get();
        $classes       = Classe::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $currentYear   = AcademicYear::where('is_current', true)->first();

        return view('teacher-assignments.form', compact(
            'teacherAssignment',
            'teachers',
            'subjects',
            'classes',
            'academicYears',
            'currentYear'
        ));
    }

    /**
     * Update the specified assignment.
     */
    public function update(Request $request, ClassSubjectTeacher $teacherAssignment): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id'       => ['required', 'exists:teachers,id'],
            'subject_id'       => ['required', 'exists:subjects,id'],
            'class_id'  => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        // Vérifier l'unicité (en excluant l'enregistrement actuel)
        $exists = ClassSubjectTeacher::where($validated)
            ->where('id', '!=', $teacherAssignment->id)
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Cette assignation existe déjà pour cette année académique.');
        }

        $teacherAssignment->update($validated);

        return redirect()
            ->route('teacher-assignments.index')
            ->with('success', 'L\'assignation a été mise à jour avec succès.');
    }

    /**
     * Remove the specified assignment.
     */
    public function destroy(ClassSubjectTeacher $teacherAssignment): RedirectResponse
    {
        $teacherAssignment->delete();

        return redirect()
            ->route('teacher-assignments.index')
            ->with('success', 'L\'assignation a été supprimée avec succès.');
    }
}