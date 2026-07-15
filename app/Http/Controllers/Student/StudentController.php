<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\ParentUser;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    // Inscription, profil complet, assignation classe, association parent, photo upload, génération matricule auto, export Excel.
    public function __construct(protected StudentService $studentService){
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

    public function store(StudentRequest $request): RedirectResponse
    {

        $this->studentService->store($request->validated());

        return redirect()
            ->route('students.index')
            ->with('success', __('Student created successfully.'));
    }
    
    public function show() : View{
        return view('students.show');
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
}