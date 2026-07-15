<?php

namespace App\Support\Concerns;

use App\Models\AcademicYear;
use App\Models\Term;

/**
 * Mutualise la résolution de l'année académique active et du trimestre
 * en cours, utilisée par les 4 dashboards (admin, étudiant, enseignant, parent).
 */
trait ResolvesAcademicPeriod
{
    protected function activeAcademicYear(): ?AcademicYear
    {
        return AcademicYear::query()->active()->first();
    }

    protected function currentTerm(?int $academicYearId): ?Term
    {
        if (! $academicYearId) {
            return null;
        }

        return Term::query()
            ->where('academic_year_id', $academicYearId)
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->first();
    }
}