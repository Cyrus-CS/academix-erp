<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classe;
use App\Models\Student;
use App\Models\Teacher;
use App\Notifications\AbsenceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $user  = Auth::user();
        $activeYear = AcademicYear::active()->first();

        $query = Attendance::with(['student.user', 'student.classe', 'classe'])
            ->latest('date');

        // Enseignant : limiter à ses classes
        if ($user->hasRole('Teacher')) {
            $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
            $query->whereHas('student.classe', fn($q) =>
                $q->whereHas('teachers', fn($q2) => $q2->where('teachers.id', $teacher->id))
            );
        }

        // Filtres
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->input('date'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        $attendances = $query->paginate(20)->withQueryString();
        $classes     = Classe::orderBy('name')->get();
        $students    = Student::with('user')->orderBy('student_number')->get();

        // Statistiques globales
        $stats = [
            'total'   => Attendance::count(),
            'present' => Attendance::where('status', 'present')->count(),
            'absent'  => Attendance::where('status', 'absent')->count(),
            'late'    => Attendance::where('status', 'late')->count(),
            'rate'    => $this->attendanceRate(),
        ];

        return view('attendance.index', compact(
            'attendances',
            'classes',
            'students',
            'stats',
            'activeYear'
        ));
    }

    public function create(Request $request): View
    {
        $classes     = Classe::orderBy('name')->get();
        $selectedClass = Classe::with('students.user')
            ->find($request->input('class_id'));

        $date = $request->input('date', today()->format('Y-m-d'));

        // Pré-charger les présences existantes pour cette date/classe
        $existing = [];
        if ($selectedClass && $date) {
            $existing = Attendance::where('class_id', $selectedClass->id)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id')
                ->toArray();
        }

        return view('attendance.form', compact(
            'classes',
            'selectedClass',
            'date',
            'existing'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id'       => ['required', 'exists:classes,id'],
            'date'           => ['required', 'date', 'before_or_equal:today'],
            'attendances'    => ['required', 'array'],
            'attendances.*.student_id' => ['required', 'exists:students,id'],
            'attendances.*.status'     => ['required', 'in:present,absent,late'],
            'attendances.*.note'       => ['nullable', 'string', 'max:200'],
        ], [
            'class_id.required'  => 'La classe est obligatoire.',
            'date.required'      => 'La date est obligatoire.',
            'date.before_or_equal' => 'La date ne peut pas être dans le futur.',
            'attendances.required' => 'Aucun étudiant sélectionné.',
        ]);

        DB::beginTransaction();

        try {
            $absentStudents = [];

            foreach ($validated['attendances'] as $item) {
                // Upsert : mettre à jour si existant, créer sinon
                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $item['student_id'],
                        'class_id'   => $validated['class_id'],
                        'date'       => $validated['date'],
                    ],
                    [
                        'status' => $item['status'],
                        'note'   => $item['note'] ?? null,
                    ]
                );

                if ($item['status'] === 'absent') {
                    $absentStudents[] = $item['student_id'];
                }
            }

            // Notifier les parents des absents
            if (!empty($absentStudents)) {
                $this->notifyAbsences($absentStudents, $validated['date']);
            }

            DB::commit();

            return to_route('attendance.index')
                ->with('success', 'Les présences ont été enregistrées avec succès.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function show(Attendance $attendance): View
    {
        $attendance->load(['student.user', 'classe']);

        return view('attendance.show', compact('attendance'));
    }

    public function edit(Attendance $attendance): View
    {
        $classes  = Classe::orderBy('name')->get();
        $students = Student::with('user')
            ->where('class_id', $attendance->class_id)
            ->orderBy('student_number')
            ->get();

        return view('attendance.edit', compact('attendance', 'classes', 'students'));
    }

    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:present,absent,late'],
            'note'   => ['nullable', 'string', 'max:200'],
        ]);

        $attendance->update($validated);

        return to_route('attendance.index')
            ->with('success', 'La présence a été mise à jour avec succès.');
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $attendance->delete();

        return to_route('attendance.index')
            ->with('success', 'L\'enregistrement de présence a été supprimé.');
    }

    /**
     * Taux de présence global en %.
     */
    private function attendanceRate(): float
    {
        $total   = Attendance::count();
        $present = Attendance::where('status', 'present')->count();

        return $total > 0 ? round(($present / $total) * 100, 1) : 0.0;
    }

    /**
     * Notifier les parents des étudiants absents.
     */
    private function notifyAbsences(array $studentIds, string $date): void
    {
        try {
        $students = Student::with('parents.user')
            ->whereIn('id', $studentIds)
            ->get();

        foreach ($students as $student) {
            foreach ($student->parents as $parent) {
                if ($parent->user) {
                    $parent->user->notify(new AbsenceNotification($student, $date));
                }
            }
        }
        } catch (\Throwable $e) {
                logger()->error('Erreur notification absence : ' . $e->getMessage());
            }
    }
}