<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasReorder;
use App\Http\Requests\StudentRequest;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\ParentUser;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    // Inscription, profil complet, assignation classe, association parent, photo upload, génération matricule auto, export Excel.
    use HasReorder;

    public function __construct(protected StudentService $studentService){
        $this->reorderModel = Student::class; 
    }
    public function index(Request $request): View
    {
        $this->authorize('view students');

        $students = Student::query()

            ->with([
                'user:id,name,email',
                'classe:id,name',
                'academicYear:id,name'
            ])

            ->search($request->search)

            ->guardian($request->guardian_name)

            ->gender($request->gender)

            ->birthDate($request->birth_date)

            ->latest()
            
            ->orderBy('position')   // ← trier par position SortableJS

            ->paginate(15)

            ->withQueryString();

        return view('students.index', [

            'students' => $students,

            'classes' => Classe::orderBy('name')->get(),

            'academicYears' => AcademicYear::latest('start_date')->get(),

        ]);
    }

    public function create() : View{
        
        $student = new Student();
        $this->authorize('create', $student);
        
        return view('students.form', [
            'student' => $student,
            'academicYears' => AcademicYear::all()->toArray(),
            'classes' => Classe::all()->toArray(),
            'parents' => ParentUser::all()->toArray(),
            'activeYear' => AcademicYear::active()->first(),
        ]);
    }

    public function store(StudentRequest $request, Student $student): RedirectResponse
    {

        $this->studentService->store($request->validated());
        

        return redirect()
            ->route('students.index')
            ->with('success', __('Student created successfully.'));
    }
    
    public function show(Student $student) : View{
        $student->load([
            'user',
            'classe',
            'parents.user',
            'attendances'         => fn($q) => $q->latest('date')->limit(20),
            'grades.subject',
            'grades.term.academicYear',
            'payments.feeType',
            'reportCards.term.academicYear',
        ]);
        return view('students.show', compact('student'));
    }
    
    public function edit(Student $student): View
    {
        $this->authorize('update', $student);
        return view('students.form', [
            'student' => $student,
            'academicYears' => AcademicYear::all()->toArray(),
            'classes' => Classe::all()->toArray(),
            'parents' => ParentUser::all()->toArray(),
            'activeYear' => AcademicYear::active()->first(),
        ]);
            
    }
    
    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        $this->authorize('update', $student); // et non 'update students' en chaîne littérale

        $this->studentService->update($student, $request->validated());

        return redirect()
            ->route('students.index')
            ->with('success', 'Élève modifié avec succès.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->authorize('delete students');

        $this->studentService->delete($student);

        return back()->with(
            'success',
            'Élève supprimé.'
        );
    }

    public function reorder(Request $request): JsonResponse
    {
        /**$request->validate([
            'order'   => ['required', 'array', 'min:1'],
            'order.*' => ['required', 'integer', 'exists:students,id'],
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->order as $position => $id) {
                Student::where('id', $id)->update(['position' => $position + 1]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Ordre mis à jour avec succès.',
        ]);**/
        return $this->handleReorder($request);
    }
}