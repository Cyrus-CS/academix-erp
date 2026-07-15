<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
// use Illuminate\Http\Request;

class SchedulesController extends Controller
{
    public function index(): View
    {
        $activeYear    = AcademicYear::active()->first();
        $selectedClass = Classe::find(request('class_id'));

        $schedules = Schedule::with(['subject', 'teacher.user', 'classe'])
            ->where('academic_year_id', $activeYear?->id)
            ->when($selectedClass, fn($q) => $q->where('class_id', $selectedClass->id))
            ->get()
            ->groupBy('class_id');

        $classes  = Classe::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::with('user')->orderBy('id')->get();

        return view('timetables.index', [
            'schedules'     => $schedules,
            'classes'       => $classes,
            'subjects'      => $subjects,
            'teachers'      => $teachers,
            'activeYear'    => $activeYear,
            'selectedClass' => $selectedClass,
            'days'          => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        ]);
    }

    public function create(): View
    {
        $activeYear = AcademicYear::active()->first();
        $classes    = Classe::orderBy('name')->get();
        $subjects   = Subject::where('is_active', true)->orderBy('name')->get();
        $teachers   = Teacher::with('user')->orderBy('id')->get();
        $schedule   = new Schedule();

        return view('timetables.form', compact(
            'schedule',
            'classes',
            'subjects',
            'teachers',
            'activeYear'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'subject_id'       => ['required', 'exists:subjects,id'],
            'teacher_id'       => ['required', 'exists:teachers,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'day_of_week'      => ['required', 'in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi'],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'room'             => ['nullable', 'string', 'max:50'],
        ], [
            'class_id.required'    => 'La classe est obligatoire.',
            'subject_id.required'  => 'La matière est obligatoire.',
            'teacher_id.required'  => "L'enseignant est obligatoire.",
            'day_of_week.required' => 'Le jour est obligatoire.',
            'start_time.required'  => "L'heure de début est obligatoire.",
            'end_time.required'    => "L'heure de fin est obligatoire.",
            'end_time.after'       => "L'heure de fin doit être après l'heure de début.",
        ]);

        // Vérifier chevauchement de créneau
        $conflict = Schedule::where('class_id', $validated['class_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where(fn($q) => $q
                ->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
            )->exists();

        if ($conflict) {
            return back()->withInput()
                ->with('error', 'Ce créneau est déjà occupé pour cette classe.');
        }

        Schedule::create($validated);

        return to_route('timetables.index', ['class_id' => $validated['class_id']])
            ->with('success', 'Le créneau a été ajouté à l\'emploi du temps.');
    }

    public function show(Schedule $timetable): View
    {
        $timetable->load(['subject', 'teacher.user', 'classe', 'academicYear']);

        return view('timetables.show', compact('timetable'));
    }

    public function edit(Schedule $timetable): View
    {
        $activeYear = AcademicYear::active()->first();
        $classes    = Classe::orderBy('name')->get();
        $subjects   = Subject::where('is_active', true)->orderBy('name')->get();
        $teachers   = Teacher::with('user')->orderBy('id')->get();

        return view('timetables.form', [
            'schedule'   => $timetable,
            'classes'    => $classes,
            'subjects'   => $subjects,
            'teachers'   => $teachers,
            'activeYear' => $activeYear,
        ]);
    }

    public function update(Request $request, Schedule $timetable): RedirectResponse
    {
        $validated = $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'subject_id'       => ['required', 'exists:subjects,id'],
            'teacher_id'       => ['required', 'exists:teachers,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'day_of_week'      => ['required', 'in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi'],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'room'             => ['nullable', 'string', 'max:50'],
        ]);

        // Vérifier chevauchement en excluant le créneau actuel
        $conflict = Schedule::where('class_id', $validated['class_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('id', '!=', $timetable->id)
            ->where(fn($q) => $q
                ->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
            )->exists();

        if ($conflict) {
            return back()->withInput()
                ->with('error', 'Ce créneau est déjà occupé pour cette classe.');
        }

        $timetable->update($validated);

        return to_route('timetables.index', ['class_id' => $validated['class_id']])
            ->with('success', 'Le créneau a été mis à jour avec succès.');
    }

    public function destroy(Schedule $timetable): RedirectResponse
    {
        $classId = $timetable->class_id;
        $timetable->delete();

        return to_route('timetables.index', ['class_id' => $classId])
            ->with('success', 'Le créneau a été supprimé de l\'emploi du temps.');
    }

    /**
     * Déplacer un créneau via drag & drop (AJAX).
     */
    public function move(Request $request, Schedule $schedule): JsonResponse
    {
        $validated = $request->validate([
            'day_of_week' => ['required', 'in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi'],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        // Vérifier chevauchement
        $conflict = Schedule::where('class_id', $schedule->class_id)
            ->where('academic_year_id', $schedule->academic_year_id)
            ->where('day_of_week', $validated['day_of_week'])
            ->where('id', '!=', $schedule->id)
            ->where(fn($q) => $q
                ->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
            )->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Ce créneau est déjà occupé.',
            ], 422);
        }

        $schedule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Créneau déplacé avec succès.',
        ]);
    }
}