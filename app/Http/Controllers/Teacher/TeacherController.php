<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherRequest;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherContract;
use App\Models\User;
use App\Services\TeacherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeacherController extends Controller
{

    public function __construct(protected TeacherService $teacherService){
        
    }
    /**
     * Display a listing of teachers.
     */
    public function index(Request $request): View
    {
        $activeYear = AcademicYear::active()->first();

        $query = Teacher::query()
            ->with(['user', 'contracts', 'assignments.subject'])
            ->withCount(['assignments', 'grades', 'contracts'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($sub) =>
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                )->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('qualification')) {
            $query->where('qualification', $request->input('qualification'));
        }

        $teachers = $query->paginate(15)->withQueryString();

        $totalTeachers   = Teacher::count();
        $activeContracts = TeacherContract::whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();
        $subjects        = Subject::orderBy('name')->get();
        $newThisMonth    = Teacher::newTeachersInMonth();

        return view('teachers.index', compact(
            'teachers', 'totalTeachers', 'activeContracts',
            'subjects', 'newThisMonth', 'activeYear'
        ));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create(): View
    {
        $teacher = new Teacher();

        return view('teachers.form', compact('teacher'));
    }

    /**
     * Store a newly created teacher.
     */
    public function store(TeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $this->teacherService->store($request->validated());

            return redirect()
                ->route('teachers.show', $teacher)
                ->with('success', "L'enseignant « {$teacher->user->name} » a été créé avec succès.");
        }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher): View
    {
        $teacher->load([
            'user',
            'assignments.subject',
            'assignments.schoolClass',
            'assignments.academicYear',
            'contracts' => fn($q) => $q->latest()->limit(5),
            'grades'    => fn($q) => $q->with('student.user', 'subject')->latest()->limit(10),
        ]);

        $stats = [
            'total_classes'      => $teacher->assignments()->distinct('class_id')->count(),
            'total_subjects'     => $teacher->assignments()->distinct('subject_id')->count(),
            'total_grades'       => $teacher->grades()->count(),
            'active_contract'    => $teacher->contracts()->where('status', 'active')->first(),
        ];

        return view('teachers.show', compact('teacher', 'stats'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('teachers.form', compact('teacher'));
    }

    /**
     * Update the specified teacher.
     */
    public function update(TeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $teacher->load('user');

        $this->teacherService->update($teacher, $request->validated());

            return redirect()
                ->route('teachers.show', $teacher)
                ->with('success', "Le profil de « {$teacher->user->name} » a été mis à jour avec succès.");
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->load('user');

        // Vérifier les dépendances
        if ($teacher->grades()->exists()) {
            return redirect()
                ->route('teachers.index')
                ->with('error', "Impossible de supprimer : des notes sont associées à cet enseignant.");
        }

        if ($teacher->contracts()->where('status', 'active')->exists()) {
            return redirect()
                ->route('teachers.index')
                ->with('error', "Impossible de supprimer : l'enseignant a un contrat actif.");
        }

        DB::beginTransaction();

        try {
            $name = $teacher->user->name;

            // Supprimer l'avatar
            if ($teacher->avatar) {
                Storage::disk('public')->delete($teacher->avatar);
            }

            $teacher->delete();
            $teacher->user->delete();

            DB::commit();

            return redirect()
                ->route('teachers.index')
                ->with('success', "L'enseignant « {$name} » a été supprimé avec succès.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route('teachers.index')
                ->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer', 'exists:teachers,id'],
        ]);

        foreach ($request->input('order') as $position => $id) {
            Teacher::where('id', $id)->update(['position' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }
}