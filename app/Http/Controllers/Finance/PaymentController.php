<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use App\Models\Payment;
use App\Models\Student;
use App\Notifications\PaymentConfirmedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
// use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request): View
    {
        $query = Payment::query()
            ->with([
                'student.user',
                'feeType',
            ])
            ->latest('paid_at');

        // Filtres
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        if ($request->filled('fee_type_id')) {
            $query->where('fee_type_id', $request->input('fee_type_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->input('date_to'));
        }

        // Recherche par référence
        if ($request->filled('search')) {
            $query->where('transaction_reference', 'like', '%' . $request->input('search') . '%');
        }

        $payments = $query->paginate(20)->withQueryString();

        // Statistiques globales
        $stats = [
            'total_collected' => Payment::where('status', 'paid')->sum('amount'),
            'pending_amount'  => Payment::where('status', 'pending')->sum('amount'),
            'overdue_count'   => Payment::where('status', 'overdue')->count(),
            'today_collected' => Payment::where('status', 'paid')
                                        ->whereDate('paid_at', today())
                                        ->sum('amount'),
        ];

        $students  = Student::with('user')->orderBy('student_number')->get();
        $feeTypes  = FeeType::where('is_active', true)->orderBy('name')->get();

        return view('payments.index', compact('payments', 'stats', 'students', 'feeTypes'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(): View
    {
        $payment  = new Payment();
        $students = Student::with('user')->orderBy('student_number')->get();
        $feeTypes = FeeType::where('is_active', true)->orderBy('name')->get();

        return view('payments.form', compact('payment', 'students', 'feeTypes'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'            => ['required', 'exists:students,id'],
            'fee_type_id'           => ['required', 'exists:fee_types,id'],
            'amount'                => ['required', 'numeric', 'min:0'],
            'payment_method'        => ['required', 'in:cash,bank_transfer,mobile_money,check,card'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'status'                => ['required', 'in:paid,pending,overdue,cancelled'],
            'paid_at'               => ['required', 'date'],
            'notes'                 => ['nullable', 'string', 'max:500'],
        ], [
            'student_id.required'     => "L'étudiant est obligatoire.",
            'fee_type_id.required'    => 'Le type de frais est obligatoire.',
            'amount.required'         => 'Le montant est obligatoire.',
            'payment_method.required' => 'Le mode de paiement est obligatoire.',
            'payment_method.in'       => 'Le mode de paiement sélectionné est invalide.',
            'status.required'         => 'Le statut est obligatoire.',
            'paid_at.required'        => 'La date de paiement est obligatoire.',
        ]);

        DB::beginTransaction();

        try {
            // Générer la référence si absente
            if (empty($validated['transaction_reference'])) {
                $validated['transaction_reference'] = $this->generateReference();
            }

            $payment = Payment::create($validated);

            // Générer le QR Code pour vérification du reçu
            $qrData = route('payments.show', $payment->id) .
                      '?ref=' . $payment->transaction_reference;

            $payment->update([
                'qr_code' => base64_encode(
                    QrCode::format('png')->size(200)->generate($qrData)
                ),
            ]);

            // Envoyer notification si paiement validé
            if ($payment->status === 'paid') {
                $this->sendPaymentNotification($payment);
            }

            DB::commit();

            return redirect()
                ->route('payments.show', $payment)
                ->with('success', "Le paiement a été enregistré avec succès. Référence : {$payment->transaction_reference}");

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified payment (reçu).
     */
    public function show(Payment $payment): View
    {
        $payment->load([
            'student.user',
            'student.parents.user',
            'feeType',
        ]);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment): View
    {
        $students = Student::with('user')->orderBy('student_number')->get();
        $feeTypes = FeeType::where('is_active', true)->orderBy('name')->get();

        return view('payments.form', compact('payment', 'students', 'feeTypes'));
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'            => ['required', 'exists:students,id'],
            'fee_type_id'           => ['required', 'exists:fee_types,id'],
            'amount'                => ['required', 'numeric', 'min:0'],
            'payment_method'        => ['required', 'in:cash,bank_transfer,mobile_money,check,card'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'status'                => ['required', 'in:paid,pending,overdue,cancelled'],
            'paid_at'               => ['required', 'date'],
            'notes'                 => ['nullable', 'string', 'max:500'],
        ]);

        $wasNotPaid = $payment->status !== 'paid';

        DB::beginTransaction();

        try {
            $payment->update($validated);

            // Envoyer notification si le statut passe à "payé"
            if ($wasNotPaid && $payment->status === 'paid') {
                $this->sendPaymentNotification($payment);
            }

            DB::commit();

            return redirect()
                ->route('payments.show', $payment)
                ->with('success', 'Le paiement a été mis à jour avec succès.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    /**
     * Remove the specified payment.
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        if ($payment->status === 'paid') {
            return redirect()
                ->route('payments.index')
                ->with('error', 'Impossible de supprimer un paiement validé.');
        }

        $ref = $payment->transaction_reference;
        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with('success', "Le paiement « {$ref} » a été supprimé avec succès.");
    }

    /**
     * Télécharger le reçu PDF avec QR Code.
     */
    public function downloadReceipt(Payment $payment): Response
    {
        $payment->load([
            'student.user',
            'feeType',
        ]);

        $pdf = Pdf::loadView('payments.receipt-pdf', compact('payment'))
                  ->setPaper('a5', 'portrait');

        return $pdf->download("recu-{$payment->transaction_reference}.pdf");
    }

    /**
     * Générer une référence unique de transaction.
     */
    private function generateReference(): string
    {
        do {
            $ref = 'PAY-' . strtoupper(Str::random(8)) . '-' . now()->format('ymd');
        } while (Payment::where('transaction_reference', $ref)->exists());

        return $ref;
    }

    /**
     * Envoyer la notification de paiement aux parents.
     */
    private function sendPaymentNotification(Payment $payment): void
    {
        try {
            $payment->loadMissing(['student.parents.user', 'feeType']);

            $parents = $payment->student->parents;

            if ($parents->isNotEmpty()) {
                Notification::send(
                    $parents->map->user,
                    new PaymentConfirmedNotification($payment)
                );
            }
        } catch (\Throwable $e) {
            // Logger l'erreur sans bloquer le process
            logger()->error('Erreur notification paiement : ' . $e->getMessage());
        }
    }
}