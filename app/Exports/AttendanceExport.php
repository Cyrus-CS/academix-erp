<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AttendanceExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private readonly ?string $startDate = null,
        private readonly ?string $endDate = null,
        private readonly ?int $classId = null,
        private readonly ?string $status = null,
    ) {}

    public function title(): string
    {
        return 'Présences';
    }

    public function query()
    {
        return Attendance::query()
            ->with([
                'student.user',
                'student.classe',
                'subject',
                'teacher.user',
            ])
            ->when(
                $this->startDate,
                fn($q) => $q->whereDate('date', '>=', $this->startDate)
            )
            ->when(
                $this->endDate,
                fn($q) => $q->whereDate('date', '<=', $this->endDate)
            )
            ->when(
                $this->classId,
                fn($q) => $q->whereHas('student', fn($s) => $s->where('class_id', $this->classId))
            )
            ->when(
                $this->status,
                fn($q) => $q->where('status', $this->status)
            )
            ->orderByDesc('date')
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'Date',
            'Matricule',
            'Nom de l\'étudiant',
            'Classe',
            'Matière',
            'Statut',
            'Minutes de retard',
            'Motif / Justification',
            'Enseignant',
            'Saisi le',
        ];
    }

    public function map($attendance): array
    {
        $statusLabel = match($attendance->status) {
            'present' => 'Présent',
            'absent'  => 'Absent',
            'late'    => 'En retard',
            default   => ucfirst($attendance->status ?? '—'),
        };

        return [
            $attendance->date?->format('d/m/Y') ?? '—',
            $attendance->student?->matricule ?? '—',
            $attendance->student?->user?->name ?? '—',
            $attendance->student?->classe?->name ?? '—',
            $attendance->subject?->name ?? '—',
            $statusLabel,
            $attendance->late_minutes ?? 0,
            $attendance->reason ?? '',
            $attendance->teacher?->user?->name ?? '—',
            $attendance->created_at?->format('d/m/Y H:i') ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();

        // Colorier les lignes selon le statut
        if ($highestRow > 1) {
            for ($row = 2; $row <= $highestRow; $row++) {
                $status = $sheet->getCell("F{$row}")->getValue();

                $color = match($status) {
                    'Présent'    => 'FFD1FAE5', // emerald clair
                    'Absent'     => 'FFFEE2E2', // red clair
                    'En retard'  => 'FFFEF3C7', // amber clair
                    default      => 'FFFFFFFF',
                };

                $sheet->getStyle("A{$row}:J{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($color);
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
                    'startColor' => ['argb' => 'FFF59E0B'],
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
            'A' => 14,
            'B' => 15,
            'C' => 25,
            'D' => 14,
            'E' => 20,
            'F' => 14,
            'G' => 16,
            'H' => 28,
            'I' => 22,
            'J' => 18,
        ];
    }
}