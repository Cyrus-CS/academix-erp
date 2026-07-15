<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherContract;
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
        $query = TeacherContract::query()
            ->with([
                'teacher.user',
                'subject',
                'schoolClass',
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
            $query->where('school_class_id', $request->input('class_id'));
        }

        // Filtre année académique
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->input('academic_year_id'));
        } else {
            // Par défaut : année courante
            $currentYear = AcademicYear::where('is_current', true)->first();
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
        $assignment    = new TeacherContract();
        $teachers      = Teacher::with('user')->orderBy('employee_number')->get();
        $subjects      = Subject::where('is_active', true)->orderBy('name')->get();
        $classes       = Classe::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $currentYear   = AcademicYear::where('is_current', true)->first();

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
            'school_class_id'  => ['required', 'exists:school_classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ], [
            'teacher_id.required'       => "L'enseignant est obligatoire.",
            'subject_id.required'       => 'La matière est obligatoire.',
            'school_class_id.required'  => 'La classe est obligatoire.',
            'academic_year_id.required' => "L'année académique est obligatoire.",
        ]);

        // Vérifier l'unicité de l'assignation
        $exists = TeacherContract::where($validated)->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Cette assignation existe déjà pour cette année académique.');
        }

        // Vérifier qu'un autre enseignant n'enseigne pas déjà cette matière dans cette classe
        $conflict = TeacherContract::where([
            'subject_id'       => $validated['subject_id'],
            'school_class_id'  => $validated['school_class_id'],
            'academic_year_id' => $validated['academic_year_id'],
        ])->where('teacher_id', '!=', $validated['teacher_id'])->exists();

        if ($conflict) {
            return redirect()
                ->back()
                ->withInput()
                ->with('warning', 'Un autre enseignant est déjà assigné à cette matière dans cette classe.');
        }

        TeacherContract::create($validated);

        return redirect()
            ->route('teacher-assignments.index')
            ->with('success', 'L\'assignation a été créée avec succès.');
    }

    /**
     * Display the specified assignment.
     */
    public function show(TeacherContract $teacherAssignment): View
    {
        $teacherAssignment->load([
            'teacher.user',
            'subject',
            'schoolClass.students.user',
            'academicYear',
        ]);

        return view('teacher-assignments.show', compact('teacherAssignment'));
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(TeacherContract $teacherAssignment): View
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
    public function update(Request $request, TeacherContract $teacherAssignment): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id'       => ['required', 'exists:teachers,id'],
            'subject_id'       => ['required', 'exists:subjects,id'],
            'school_class_id'  => ['required', 'exists:school_classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        // Vérifier l'unicité (en excluant l'enregistrement actuel)
        $exists = TeacherContract::where($validated)
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
    public function destroy(TeacherContract $teacherAssignment): RedirectResponse
    {
        $teacherAssignment->delete();

        return redirect()
            ->route('teacher-assignments.index')
            ->with('success', 'L\'assignation a été supprimée avec succès.');
    }
}