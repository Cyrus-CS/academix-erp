<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StudentsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private readonly ?int    $classId = null,
        private readonly ?string $gender         = null,
        private readonly ?int    $academicYearId = null,
        private readonly ?string $search         = null,
    ) {}

    public function query()
    {
        $query = Student::with(['user', 'classe', 'academicYear'])
            ->latest();

        if ($this->search) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
            )->orWhere('matricule', 'like', "%{$this->search}%");
        }

        if ($this->classId) {
            $query->where('class_id', $this->classId);
        }

        if ($this->gender) {
            $query->where('gender', $this->gender);
        }

        if ($this->academicYearId) {
            $query->where('academic_year_id', $this->academicYearId);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            '#',
            'Matricule',
            'Nom complet',
            'Email',
            'Téléphone',
            'Genre',
            'Date de naissance',
            'Âge',
            'Classe',
            'Année académique',
            'Adresse',
            'Taux de présence',
            'Moyenne générale',
            'Date inscription',
        ];
    }

    public function map($student): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $student->matricule,
            $student->user->name ?? '—',
            $student->user->email ?? '—',
            $student->user->phone ?? '—',
            $student->gender === 'male' ? 'Masculin' : 'Féminin',
            $student->birth_date?->format('d/m/Y') ?? '—',
            $student->age ?? '—',
            $student->classe->name ?? '—',
            $student->academicYear->name ?? '—',
            $student->address ?? '—',
            $student->attendanceRate() . '%',
            $student->average() > 0 ? $student->average() . '/20' : '—',
            $student->created_at->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // En-tête : fond bleu, texte blanc, gras
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size'  => 11,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2563EB'],
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
            'A' => 6,
            'B' => 18,
            'C' => 28,
            'D' => 30,
            'E' => 16,
            'F' => 12,
            'G' => 18,
            'H' => 6,
            'I' => 16,
            'J' => 18,
            'K' => 30,
            'L' => 16,
            'M' => 16,
            'N' => 16,
        ];
    }

    public function title(): string
    {
        return 'Élèves';
    }
}