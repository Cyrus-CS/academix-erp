<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reçu de paiement — {{ $payment->transaction_reference }}</title>
    <style>
    /* ── Reset & Base ───────────────────────────────────────── */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
        font-size: 12px;
        color: #1e293b;
        background: #ffffff;
        line-height: 1.5;
    }

    /* ── Page ───────────────────────────────────────────────── */
    .page {
        width: 100%;
        min-height: 100%;
        padding: 28px 32px;
        position: relative;
    }

    /* ── Watermark ──────────────────────────────────────────── */
    .watermark {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-35deg);
        font-size: 72px;
        font-weight: 900;
        color: rgba(37, 99, 235, 0.04);
        letter-spacing: 4px;
        white-space: nowrap;
        pointer-events: none;
        z-index: 0;
    }

    /* ── Header ─────────────────────────────────────────────── */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid #2563eb;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .brand-logo {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #2563eb, #10b981);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        font-weight: 900;
        text-align: center;
        line-height: 44px;
    }

    .brand-name {
        font-size: 18px;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: -0.3px;
    }

    .brand-sub {
        font-size: 10px;
        color: #64748b;
        margin-top: 1px;
    }

    .receipt-badge {
        text-align: right;
    }

    .receipt-title {
        font-size: 20px;
        font-weight: 800;
        color: #2563eb;
        letter-spacing: -0.5px;
    }

    .receipt-ref {
        font-size: 10px;
        color: #64748b;
        margin-top: 3px;
        font-family: 'Courier New', monospace;
    }

    .receipt-date {
        font-size: 10px;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* ── Status Banner ──────────────────────────────────────── */
    .status-banner {
        border-radius: 10px;
        padding: 10px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .status-banner.paid {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 1px solid #6ee7b7;
    }

    .status-banner.pending {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1px solid #fcd34d;
    }

    .status-banner.cancelled {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: 1px solid #fca5a5;
    }

    .status-text {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .status-text.paid {
        color: #059669;
    }

    .status-text.pending {
        color: #d97706;
    }

    .status-text.cancelled {
        color: #dc2626;
    }

    .status-amount {
        font-size: 22px;
        font-weight: 900;
        letter-spacing: -0.5px;
    }

    .status-amount.paid {
        color: #059669;
    }

    .status-amount.pending {
        color: #d97706;
    }

    .status-amount.cancelled {
        color: #dc2626;
    }

    /* ── Grid 2 colonnes ────────────────────────────────────── */
    .grid-2 {
        display: table;
        width: 100%;
        margin-bottom: 20px;
        border-spacing: 0 0;
    }

    .grid-2 .col {
        display: table-cell;
        width: 50%;
        vertical-align: top;
        padding-right: 10px;
    }

    .grid-2 .col:last-child {
        padding-right: 0;
        padding-left: 10px;
    }

    /* ── Section Card ────────────────────────────────────────── */
    .card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 16px;
    }

    .card-header {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #2563eb;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e2e8f0;
    }

    .info-row {
        display: table;
        width: 100%;
        margin-bottom: 6px;
    }

    .info-label {
        display: table-cell;
        font-size: 10px;
        color: #64748b;
        font-weight: 500;
        width: 45%;
    }

    .info-value {
        display: table-cell;
        font-size: 11px;
        color: #1e293b;
        font-weight: 600;
        text-align: right;
    }

    /* ── Détail paiement ────────────────────────────────────── */
    .detail-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }

    .detail-table thead tr {
        background: #2563eb;
        color: white;
    }

    .detail-table thead th {
        padding: 8px 12px;
        font-size: 10px;
        font-weight: 700;
        text-align: left;
        letter-spacing: 0.3px;
    }

    .detail-table thead th:last-child {
        text-align: right;
    }

    .detail-table tbody tr {
        background: #f8fafc;
    }

    .detail-table tbody tr:nth-child(even) {
        background: #f1f5f9;
    }

    .detail-table tbody td {
        padding: 9px 12px;
        font-size: 11px;
        color: #334155;
        border-bottom: 1px solid #e2e8f0;
    }

    .detail-table tbody td:last-child {
        text-align: right;
        font-weight: 700;
        color: #1e293b;
    }

    .detail-table tfoot tr {
        background: #1e293b;
    }

    .detail-table tfoot td {
        padding: 10px 12px;
        color: white;
        font-size: 12px;
        font-weight: 800;
    }

    .detail-table tfoot td:last-child {
        text-align: right;
        font-size: 14px;
        color: #10b981;
    }

    /* ── Méthode paiement Badge ─────────────────────────────── */
    .method-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .method-cash {
        background: #dcfce7;
        color: #16a34a;
    }

    .method-card {
        background: #dbeafe;
        color: #2563eb;
    }

    .method-transfer {
        background: #f0fdf4;
        color: #15803d;
    }

    .method-mobile_money {
        background: #fef3c7;
        color: #d97706;
    }

    .method-cheque {
        background: #f3e8ff;
        color: #7c3aed;
    }

    /* ── QR Code + Footer ────────────────────────────────────── */
    .bottom-section {
        display: table;
        width: 100%;
        margin-top: 8px;
    }

    .bottom-left {
        display: table-cell;
        vertical-align: bottom;
        width: 65%;
    }

    .bottom-right {
        display: table-cell;
        vertical-align: bottom;
        text-align: right;
        width: 35%;
    }

    .footer-note {
        font-size: 9px;
        color: #94a3b8;
        line-height: 1.6;
    }

    .footer-note strong {
        color: #64748b;
    }

    .qr-container {
        display: inline-block;
        padding: 8px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }

    .qr-label {
        font-size: 8px;
        color: #94a3b8;
        text-align: center;
        margin-top: 4px;
    }

    /* ── Divider ────────────────────────────────────────────── */
    .divider {
        border: none;
        border-top: 1px dashed #e2e8f0;
        margin: 16px 0;
    }

    /* ── Signature ligne ────────────────────────────────────── */
    .signature-section {
        display: table;
        width: 100%;
        margin-top: 20px;
    }

    .signature-col {
        display: table-cell;
        width: 33.33%;
        text-align: center;
        padding: 0 8px;
    }

    .signature-line {
        border-top: 1px solid #cbd5e1;
        margin-bottom: 6px;
        padding-top: 6px;
    }

    .signature-label {
        font-size: 9px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ── Stamp ──────────────────────────────────────────────── */
    .stamp {
        position: absolute;
        top: 120px;
        right: 40px;
        width: 90px;
        height: 90px;
        border: 3px solid rgba(16, 185, 129, 0.25);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: rotate(-15deg);
        opacity: 0.6;
    }

    .stamp-inner {
        font-size: 10px;
        font-weight: 900;
        color: #10b981;
        text-align: center;
        line-height: 1.3;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    </style>
</head>

<body>

    @php
    $statusMap = [
    'paid' => ['label' => 'Payé', 'class' => 'paid'],
    'pending' => ['label' => 'En attente', 'class' => 'pending'],
    'cancelled' => ['label' => 'Annulé', 'class' => 'cancelled'],
    ];

    $methodMap = [
    'cash' => ['label' => 'Espèces', 'class' => 'method-cash'],
    'card' => ['label' => 'Carte bancaire', 'class' => 'method-card'],
    'transfer' => ['label' => 'Virement', 'class' => 'method-transfer'],
    'mobile_money' => ['label' => 'Mobile Money', 'class' => 'method-mobile_money'],
    'cheque' => ['label' => 'Chèque', 'class' => 'method-cheque'],
    ];

    $status = $statusMap[$payment->status ?? 'pending'] ?? $statusMap['pending'];
    $method = $methodMap[$payment->payment_method ?? 'cash'] ?? $methodMap['cash'];
    $studentName = $payment->student->user->name ?? '—';
    $matricule = $payment->student->matricule ?? '—';
    @endphp

    <div class="page">

        {{-- Watermark --}}
        <div class="watermark">SCHOOL ERP</div>

        {{-- ── HEADER ────────────────────────────────────────────── --}}
        <div class="header">
            <div class="brand">
                <div class="brand-logo">S</div>
                <div>
                    <div class="brand-name">School ERP</div>
                    <div class="brand-sub">Système de gestion scolaire</div>
                </div>
            </div>
            <div class="receipt-badge">
                <div class="receipt-title">REÇU DE PAIEMENT</div>
                <div class="receipt-ref">Réf : {{ $payment->transaction_reference }}</div>
                <div class="receipt-date">
                    Émis le {{ $payment->created_at->translatedFormat('d F Y à H:i') }}
                </div>
            </div>
        </div>

        {{-- ── STATUS BANNER ──────────────────────────────────────── --}}
        <div class="status-banner {{ $status['class'] }}">
            <div>
                <span class="status-text {{ $status['class'] }}">
                    ● Statut : {{ $status['label'] }}
                </span>
                <div style="font-size: 10px; color: #64748b; margin-top: 3px;">
                    {{ $payment->paid_at?->translatedFormat('d F Y') ?? 'Non encore payé' }}
                </div>
            </div>
            <div class="status-amount {{ $status['class'] }}">
                {{ number_format($payment->amount, 0, ',', ' ') }}
                <span style="font-size: 13px;">FCFA</span>
            </div>
        </div>

        {{-- ── INFOS GRID ─────────────────────────────────────────── --}}
        <div class="grid-2">

            {{-- Colonne Élève --}}
            <div class="col">
                <div class="card">
                    <div class="card-header">Informations de l'élève</div>

                    <div class="info-row">
                        <span class="info-label">Nom complet</span>
                        <span class="info-value">{{ $studentName }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Matricule</span>
                        <span class="info-value" style="font-family: 'Courier New', monospace; font-size: 10px;">
                            {{ $matricule }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Classe</span>
                        <span class="info-value">
                            {{ $payment->student->classe->name ?? '—' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Année scolaire</span>
                        <span class="info-value">
                            {{ config('school.current_year', '2024 – 2025') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Colonne Paiement --}}
            <div class="col">
                <div class="card">
                    <div class="card-header">Détails du paiement</div>

                    <div class="info-row">
                        <span class="info-label">Type de frais</span>
                        <span class="info-value">{{ $payment->feeType->name ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Méthode</span>
                        <span class="info-value">
                            <span class="method-badge {{ $method['class'] }}">
                                {{ $method['label'] }}
                            </span>
                        </span>
                    </div>
                    @if($payment->transaction_reference)
                    <div class="info-row">
                        <span class="info-label">Référence</span>
                        <span class="info-value" style="font-family: 'Courier New', monospace; font-size: 9px;">
                            {{ $payment->transaction_reference }}
                        </span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Date paiement</span>
                        <span class="info-value">
                            {{ $payment->paid_at?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TABLEAU DÉTAIL ──────────────────────────────────────── --}}
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="border-radius: 8px 0 0 0; width: 60%;">Désignation</th>
                    <th style="border-radius: 0 8px 0 0; text-align: right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $payment->feeType->name ?? 'Frais scolaires' }}</strong>
                        @if($payment->feeType->description ?? false)
                        <br>
                        <span style="font-size: 9px; color: #94a3b8;">
                            {{ $payment->feeType->description }}
                        </span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td style="font-size: 11px; color: #94a3b8;">
                        Montant total réglé
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            </tfoot>
        </table>

        <hr class="divider">

        {{-- ── SECTION BAS : Notes + QR ───────────────────────────── --}}
        <div class="bottom-section">
            <div class="bottom-left">

                {{-- Notes --}}
                @if($payment->notes ?? false)
                <div style="margin-bottom: 12px;">
                    <div style="font-size: 9px; font-weight: 700; text-transform: uppercase;
                            letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">
                        Note
                    </div>
                    <div style="font-size: 10px; color: #475569; font-style: italic;
                            background: #f8fafc; padding: 8px 10px; border-radius: 6px;
                            border-left: 3px solid #2563eb;">
                        {{ $payment->notes }}
                    </div>
                </div>
                @endif

                <div class="footer-note">
                    <strong>School ERP</strong> | Système de gestion scolaire<br>
                    Ce reçu est généré automatiquement et fait foi.<br>
                    Conservez-le précieusement pour vos archives.<br><br>
                    <strong>Vérification :</strong> Scannez le QR code ci-contre<br>
                    pour valider l'authenticité de ce reçu.
                </div>
            </div>

            <div class="bottom-right">
                {{-- QR Code --}}
                @php
                $verifyUrl = url("/payments/{$payment->id}/verify");
                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->size(90)
                ->errorCorrection('H')
                ->generate($verifyUrl);
                $qrBase64 = base64_encode($qrCode);
                @endphp

                <div class="qr-container">
                    <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code de vérification" width="90"
                        height="90" />
                </div>
                <div class="qr-label">Scanner pour vérifier</div>
            </div>
        </div>

        {{-- ── SIGNATURES ──────────────────────────────────────────── --}}
        <div class="signature-section" style="margin-top: 24px;">
            <div class="signature-col">
                <div class="signature-line"></div>
                <div class="signature-label">Caissier / Agent</div>
            </div>
            <div class="signature-col">
                <div class="signature-line"></div>
                <div class="signature-label">Cachet établissement</div>
            </div>
            <div class="signature-col">
                <div class="signature-line"></div>
                <div class="signature-label">Signature parent / élève</div>
            </div>
        </div>

    </div>

</body>

</html>