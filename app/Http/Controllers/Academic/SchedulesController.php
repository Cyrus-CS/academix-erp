<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\MoveScheduleRequest;
use App\Http\Requests\Schedule\TimesTableRequest;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
            'days'  => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        ]);
    }

    public function create(Request $request): View
    {
        $activeYear = AcademicYear::active()->first();
        $classes    = Classe::orderBy('name')->get();
        $subjects   = Subject::where('is_active', true)->orderBy('name')->get();
        $teachers   = Teacher::with('user')->orderBy('id')->get();

        $schedule = new Schedule();
        $schedule->class_id = $request->query('class_id');

        return view('timetables.form', compact(
            'schedule',
            'classes',
            'subjects',
            'teachers',
            'activeYear'
        ));
    }

    public function store(TimesTableRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // ── Conversion nom du jour → entier (ce qui manquait) ──
        $validated['day_of_week'] = Schedule::DAYS[$validated['day_of_week']];

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

    public function update(TimesTableRequest $request, Schedule $timetable): RedirectResponse
    {
        $validated = $request->validated();

         // ── Conversion nom du jour → entier (ce qui manquait) ──
        $validated['day_of_week'] = Schedule::DAYS[$validated['day_of_week']];

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
    public function move(MoveScheduleRequest $request, Schedule $schedule): JsonResponse
    {
        $validated = $request->validated();

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

    /**
     * Générer le PDF de l'emploi du temps d'une classe.
     */
    public function print(Request $request)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        $activeYear    = AcademicYear::active()->first();
        $selectedClass = Classe::findOrFail($validated['class_id']);

        $schedules = Schedule::with(['subject', 'teacher.user', 'classe'])
            ->where('academic_year_id', $activeYear?->id)
            ->where('class_id', $selectedClass->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $pdf = Pdf::loadView('timetables.pdf', [
            'schedules'     => $schedules,
            'selectedClass' => $selectedClass,
            'activeYear'    => $activeYear,
            'days'          => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        ])->setPaper('a4', 'landscape');

        $filename = 'emploi-du-temps-' . str($selectedClass->name)->slug() . '.pdf';

        return $pdf->stream($filename);
        // ou ->download($filename) pour forcer le téléchargement au lieu de l'ouvrir dans l'onglet
    }
}