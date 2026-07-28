<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithSummaryRow;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PaymentsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithColumnFormatting,
    ShouldAutoSize
{
    public function __construct(
        private readonly ?string $status = null,
        private readonly ?string $startDate = null,
        private readonly ?string $endDate = null,
        private readonly ?string $paymentMethod = null,
    ) {}

    public function title(): string
    {
        return 'Paiements';
    }

    public function query()
    {
        return Payment::query()
            ->with([
                'student.user',
                'student.classe',
                'feeType',
            ])
            ->when($this->status,        fn($q) => $q->where('status', $this->status))
            ->when($this->paymentMethod, fn($q) => $q->where('payment_method', $this->paymentMethod))
            ->when(
                $this->startDate,
                fn($q) => $q->whereDate('paid_at', '>=', $this->startDate)
            )
            ->when(
                $this->endDate,
                fn($q) => $q->whereDate('paid_at', '<=', $this->endDate)
            )
            ->orderByDesc('paid_at');
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Matricule',
            'Nom de l\'étudiant',
            'Classe',
            'Type de frais',
            'Montant dû (FCFA)',
            'Montant payé (FCFA)',
            'Reste à payer (FCFA)',
            'Statut',
            'Mode de paiement',
            'Réf. transaction',
            'Date de paiement',
            'Saisi le',
        ];
    }

    public function map($payment): array
    {
        $remaining = ($payment->amount_due ?? 0) - ($payment->amount_paid ?? 0);

        $statusLabel = match($payment->status) {
            'paid'      => 'Payé',
            'pending'   => 'En attente',
            'overdue'   => 'En retard',
            'cancelled' => 'Annulé',
            'partial'   => 'Partiel',
            default     => ucfirst($payment->status ?? '—'),
        };

        $methodLabel = match($payment->payment_method) {
            'cash'          => 'Espèces',
            'bank_transfer' => 'Virement bancaire',
            'mobile_money'  => 'Mobile Money',
            'check'         => 'Chèque',
            'card'          => 'Carte bancaire',
            default         => ucfirst($payment->payment_method ?? '—'),
        };

        return [
            $payment->id,
            $payment->student?->matricule ?? '—',
            $payment->student?->user?->name ?? '—',
            $payment->student?->classe?->name ?? '—',
            $payment->feeType?->name ?? '—',
            $payment->amount_due ?? 0,
            $payment->amount_paid ?? 0,
            max(0, $remaining),
            $statusLabel,
            $methodLabel,
            $payment->transaction_reference ?? '—',
            $payment->paid_at?->format('d/m/Y') ?? '—',
            $payment->created_at?->format('d/m/Y H:i') ?? '—',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '#,##0',
            'G' => '#,##0',
            'H' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();

        if ($highestRow > 1) {
            for ($row = 2; $row <= $highestRow; $row++) {
                $status = $sheet->getCell("I{$row}")->getValue();

                $fontColor = match($status) {
                    'Payé'      => 'FF059669',
                    'En retard' => 'FFDC2626',
                    'Annulé'    => 'FF94A3B8',
                    'Partiel'   => 'FFD97706',
                    default     => 'FF1E293B',
                };

                $sheet->getStyle("I{$row}")
                    ->getFont()
                    ->getColor()
                    ->setARGB($fontColor);

                $sheet->getStyle("I{$row}")
                    ->getFont()
                    ->setBold(true);
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
                    'startColor' => ['argb' => 'FF06B6D4'],
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
            'A' => 10,
            'B' => 15,
            'C' => 25,
            'D' => 14,
            'E' => 20,
            'F' => 16,
            'G' => 16,
            'H' => 16,
            'I' => 14,
            'J' => 18,
            'K' => 22,
            'L' => 16,
            'M' => 18,
        ];
    }
}