<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\Grade;
use App\Models\ReportCard;
use App\Models\Student;
use App\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
// use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function index(): View
    {
        $activeYear  = AcademicYear::active()->first();
        $reportCards = ReportCard::with('student.user', 'student.classe', 'term', 'academicYear')
            ->paginate(8);
        $classes     = Classe::orderBy('name')->get();
        $terms       = Term::where('academic_year_id', $activeYear?->id)
            ->orderBy('start_date')
            ->get();
        $currentTerm = Term::where('academic_year_id', $activeYear?->id)
            ->where('is_current', true)
            ->first();

        return view('report-cards.index', [
            'reportCards' => $reportCards,
            'classes'     => $classes,
            'terms'       => $terms,
            'activeYear'  => $activeYear,
            'currentTerm' => $currentTerm,
            'total'       => $reportCards->total(),
            'generated'   => ReportCard::count(),
        ]);
    }

    public function create(): View
    {
        $activeYear  = AcademicYear::active()->first();
        $classes     = Classe::orderBy('name')->get();
        $terms       = Term::where('academic_year_id', $activeYear?->id)
            ->orderBy('start_date')
            ->get();
        $students    = Student::with('user')->orderBy('student_number')->get();
        $reportCard  = new ReportCard();

        return view('report-cards.form', compact(
            'reportCard',
            'classes',
            'terms',
            'students',
            'activeYear'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'       => ['required', 'exists:students,id'],
            'term_id'          => ['required', 'exists:terms,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_id'         => ['required', 'exists:classes,id'],
        ]);

        // Vérifier si un bulletin existe déjà
        $exists = ReportCard::where([
            'student_id' => $validated['student_id'],
            'term_id'    => $validated['term_id'],
        ])->exists();

        if ($exists) {
            return back()->withInput()
                ->with('error', 'Un bulletin existe déjà pour cet étudiant ce trimestre.');
        }

        DB::beginTransaction();

        try {
            $reportCard = $this->generateReportCard(
                $validated['student_id'],
                $validated['term_id'],
                $validated['academic_year_id'],
                $validated['class_id']
            );

            DB::commit();

            return to_route('report-cards.show', $reportCard)
                ->with('success', 'Le bulletin a été généré avec succès.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erreur lors de la génération : ' . $e->getMessage());
        }
    }

    public function show(ReportCard $reportCard): View
    {
        $reportCard->load([
            'student.user',
            'student.classe',
            'term.academicYear',
            'grades.subject',
        ]);

        // URL signée pour le téléchargement (valable 60 minutes)
        $downloadUrl = URL::temporarySignedRoute(
            'report-cards.download',
            now()->addMinutes(60),
            ['reportCard' => $reportCard->id]
        );

        return view('report-cards.show', compact('reportCard', 'downloadUrl'));
    }

    public function edit(ReportCard $reportCard): View
    {
        $activeYear = AcademicYear::active()->first();
        $classes    = Classe::orderBy('name')->get();
        $terms      = Term::where('academic_year_id', $activeYear?->id)->get();
        $students   = Student::with('user')->orderBy('student_number')->get();

        return view('report-cards.form', compact(
            'reportCard',
            'classes',
            'terms',
            'students',
            'activeYear'
        ));
    }

    public function update(Request $request, ReportCard $reportCard): RedirectResponse
    {
        // Recalculer les moyennes et le rang
        $reportCard->load('student');

        DB::beginTransaction();

        try {
            $this->recalculate($reportCard);

            DB::commit();

            return to_route('report-cards.show', $reportCard)
                ->with('success', 'Le bulletin a été recalculé avec succès.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur lors du recalcul : ' . $e->getMessage());
        }
    }

    public function destroy(ReportCard $reportCard): RedirectResponse
    {
        $reportCard->delete();

        return to_route('report-cards.index')
            ->with('success', 'Le bulletin a été supprimé.');
    }

    /**
     * Télécharger le bulletin en PDF (URL temporaire signée).
     */
    public function download(Request $request, ReportCard $reportCard): Response
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Lien de téléchargement invalide ou expiré.');
        }

        $reportCard->load([
            'student.user',
            'student.classe',
            'term.academicYear',
            'grades.subject',
        ]);

        $pdf = Pdf::loadView('report-cards.pdf', compact('reportCard'))
            ->setPaper('a4', 'portrait');

        $filename = sprintf(
            'bulletin-%s-%s.pdf',
            \Str::slug($reportCard->student->user->name),
            $reportCard->term->name
        );

        return $pdf->download($filename);
    }

    /**
     * Générer tous les bulletins du trimestre actif (admin/teacher).
     */
    public function generateAll(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'term_id'  => ['required', 'exists:terms,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
        ]);

        $term = Term::findOrFail($validated['term_id']);

        $studentsQuery = Student::query();

        if (!empty($validated['class_id'])) {
            $studentsQuery->where('class_id', $validated['class_id']);
        }

        $students = $studentsQuery->get();
        $count    = 0;

        DB::beginTransaction();

        try {
            foreach ($students as $student) {
                // Ne pas regénérer si déjà existant
                $exists = ReportCard::where([
                    'student_id' => $student->id,
                    'term_id'    => $term->id,
                ])->exists();

                if (!$exists) {
                    $this->generateReportCard(
                        $student->id,
                        $term->id,
                        $term->academic_year_id,
                        $student->class_id
                    );
                    $count++;
                }
            }

            DB::commit();

            return to_route('report-cards.index')
                ->with('success', "{$count} bulletin(s) généré(s) avec succès.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur lors de la génération : ' . $e->getMessage());
        }
    }

    /**
     * Générer un bulletin pour un étudiant.
     */
    private function generateReportCard(
        int $studentId,
        int $termId,
        int $academicYearId,
        int $classId
    ): ReportCard {
        // Récupérer les notes du trimestre
        $grades = Grade::where('student_id', $studentId)
            ->where('term_id', $termId)
            ->with('subject')
            ->get();

        // Calculer la moyenne générale (pondérée par coefficient)
        $totalPoints      = 0;
        $totalCoefficient = 0;

        foreach ($grades as $grade) {
            $coeff             = $grade->subject->coefficient ?? 1;
            $score20           = ($grade->score / $grade->max_score) * 20;
            $totalPoints      += $score20 * $coeff;
            $totalCoefficient += $coeff;
        }

        $average = $totalCoefficient > 0
            ? round($totalPoints / $totalCoefficient, 2)
            : 0;

        // Calculer le rang dans la classe
        $rank = $this->calculateRank($studentId, $termId, $classId, $average);

        // Appréciation
        $appreciation = $this->getAppreciation($average);

        return ReportCard::create([
            'student_id'       => $studentId,
            'term_id'          => $termId,
            'academic_year_id' => $academicYearId,
            'class_id'         => $classId,
            'average'          => $average,
            'rank'             => $rank,
            'appreciation'     => $appreciation,
            'total_students'   => Student::where('class_id', $classId)->count(),
        ]);
    }

    /**
     * Recalculer un bulletin existant.
     */
    private function recalculate(ReportCard $reportCard): void
    {
        $grades = Grade::where('student_id', $reportCard->student_id)
            ->where('term_id', $reportCard->term_id)
            ->with('subject')
            ->get();

        $totalPoints = $totalCoefficient = 0;

        foreach ($grades as $grade) {
            $coeff             = $grade->subject->coefficient ?? 1;
            $score20           = ($grade->score / $grade->max_score) * 20;
            $totalPoints      += $score20 * $coeff;
            $totalCoefficient += $coeff;
        }

        $average = $totalCoefficient > 0
            ? round($totalPoints / $totalCoefficient, 2)
            : 0;

        $rank = $this->calculateRank(
            $reportCard->student_id,
            $reportCard->term_id,
            $reportCard->class_id,
            $average
        );

        $reportCard->update([
            'average'      => $average,
            'rank'         => $rank,
            'appreciation' => $this->getAppreciation($average),
        ]);
    }

    /**
     * Calculer le rang d'un étudiant dans sa classe.
     */
    private function calculateRank(
        int $studentId,
        int $termId,
        int $classId,
        float $average
    ): int {
        // Compter les étudiants avec une meilleure moyenne
        $better = ReportCard::where('term_id', $termId)
            ->where('class_id', $classId)
            ->where('student_id', '!=', $studentId)
            ->where('average', '>', $average)
            ->count();

        return $better + 1;
    }

    /**
     * Retourner l'appréciation selon la moyenne.
     */
    private function getAppreciation(float $average): string
    {
        return match (true) {
            $average >= 18 => 'Excellent',
            $average >= 16 => 'Très bien',
            $average >= 14 => 'Bien',
            $average >= 12 => 'Assez bien',
            $average >= 10 => 'Passable',
            $average >= 8  => 'Insuffisant',
            default        => 'Très insuffisant',
        };
    }
}