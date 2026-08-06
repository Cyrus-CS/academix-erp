<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeeType\FeeTypeRequest;
use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
// use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    /**
     * Display a listing of fee types.
     */
    public function index(Request $request): View
    {
        $query = FeeType::query()
            ->withCount('payments')
            ->withSum('payments', 'amount_paid')
            ->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        // $payments_sum_amount = Payment::where('fee_types_id', )

        $feeTypes = $query->paginate(15)->withQueryString();

        return view('fee-types.index', compact('feeTypes'));
    }

    /**
     * Show the form for creating a new fee type.
     */
    public function create(): View
    {
        $feeType = new FeeType();

        return view('fee-types.form', compact('feeType'));
    }

    /**
     * Store a newly created fee type.
     */
    public function store(FeeTypeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['academic_year_id'] = AcademicYear::active()->value('id');

        FeeType::create($validated);

        return redirect()
            ->route('fee-types.index')
            ->with('success', "Le type de frais « {$validated['name']} » a été créé avec succès.");
    }

    /**
     * Display the specified fee type.
     */
    public function show(FeeType $feeType): View
    {
        $feeType->load([
            'payments' => fn($q) => $q->with('student.user')->latest()->limit(10),
        ]);

        $stats = [
            'total_payments'  => $feeType->payments()->count(),
            'total_amount'    => $feeType->payments()->sum('amount_paid'),
            'paid_count'      => $feeType->payments()->where('status', 'paid')->count(),
            'pending_count'   => $feeType->payments()->where('status', 'pending')->count(),
            'overdue_count'   => $feeType->payments()->where('status', 'overdue')->count(),
        ];

        return view('fee-types.show', compact('feeType', 'stats'));
    }

    /**
     * Show the form for editing the specified fee type.
     */
    public function edit(FeeType $feeType): View
    {
        return view('fee-types.form', compact('feeType'));
    }

    /**
     * Update the specified fee type.
     */
    public function update(FeeTypeRequest $request, FeeType $feeType): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active');
        $validated['academic_year_id'] = AcademicYear::active()->value('id');

        $feeType->update($validated);

        return redirect()
            ->route('fee-types.index')
            ->with('success', "Le type de frais « {$feeType->name} » a été mis à jour avec succès.");
    }

    /**
     * Remove the specified fee type.
     */
    public function destroy(FeeType $feeType): RedirectResponse
    {
        if ($feeType->payments()->exists()) {
            return redirect()
                ->route('fee-types.index')
                ->with('error', "Impossible de supprimer « {$feeType->name} » : des paiements y sont associés.");
        }

        $name = $feeType->name;
        $feeType->delete();

        return redirect()
            ->route('fee-types.index')
            ->with('success', "Le type de frais « {$name} » a été supprimé avec succès.");
    }
}