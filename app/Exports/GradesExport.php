<?php

namespace App\Exports;

use App\Models\Grade;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class GradesExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private readonly ?int $termId = null,
        private readonly ?int $classId = null,
        private readonly ?int $subjectId = null,
    ) {}

    public function title(): string
    {
        return 'Notes';
    }

    public function query()
    {
        return Grade::query()
            ->with([
                'student.user',
                'student.classe',
                'subject',
                'term',
                'teacher.user',
            ])
            ->when($this->termId,    fn($q) => $q->where('term_id', $this->termId))
            ->when($this->classId,   fn($q) => $q->whereHas('student', fn($s) => $s->where('class_id', $this->classId)))
            ->when($this->subjectId, fn($q) => $q->where('subject_id', $this->subjectId))
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Matricule',
            'Nom de l\'étudiant',
            'Classe',
            'Matière',
            'Trimestre',
            'Type d\'évaluation',
            'Note (/20)',
            'Coefficient',
            'Note pondérée',
            'Enseignant',
            'Date de saisie',
            'Commentaire',
        ];
    }

    public function map($grade): array
    {
        $coefficient   = $grade->coefficient ?? 1;
        $weightedScore = round($grade->score * $coefficient, 2);

        return [
            $grade->student?->matricule ?? '—',
            $grade->student?->user?->name ?? '—',
            $grade->student?->classe?->name ?? '—',
            $grade->subject?->name ?? '—',
            $grade->term?->name ?? '—',
            match($grade->type ?? '') {
                'homework'    => 'Devoir',
                'test'        => 'Interrogation',
                'exam'        => 'Examen',
                'oral'        => 'Oral',
                default       => ucfirst($grade->type ?? '—'),
            },
            $grade->score,
            $coefficient,
            $weightedScore,
            $grade->teacher?->user?->name ?? '—',
            $grade->created_at?->format('d/m/Y') ?? '—',
            $grade->comment ?? '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Mise en forme conditionnelle via callback
        $highestRow = $sheet->getHighestRow();

        // Colorier les notes en rouge si < 10
        if ($highestRow > 1) {
            for ($row = 2; $row <= $highestRow; $row++) {
                $score = $sheet->getCell("G{$row}")->getValue();
                if (is_numeric($score) && $score < 10) {
                    $sheet->getStyle("G{$row}")->getFont()
                        ->getColor()->setARGB('FFEF4444');
                    $sheet->getStyle("G{$row}")->getFont()->setBold(true);
                }
            }
        }

        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size'  => 11,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF10B981'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 15,
            'D' => 20,
            'E' => 18,
            'F' => 18,
            'G' => 12,
            'H' => 12,
            'I' => 15,
            'J' => 22,
            'K' => 15,
            'L' => 30,
        ];
    }
}