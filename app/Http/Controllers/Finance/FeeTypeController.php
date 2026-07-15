<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
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
            ->withSum('payments', 'amount')
            ->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:fee_types,name'],
            'amount'      => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'frequency'   => ['required', 'in:monthly,quarterly,yearly,one_time'],
            'is_active'   => ['boolean'],
        ], [
            'name.required'      => 'Le nom du type de frais est obligatoire.',
            'name.unique'        => 'Ce type de frais existe déjà.',
            'amount.required'    => 'Le montant est obligatoire.',
            'amount.min'         => 'Le montant doit être positif.',
            'frequency.required' => 'La fréquence est obligatoire.',
            'frequency.in'       => 'La fréquence sélectionnée est invalide.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

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
            'total_amount'    => $feeType->payments()->sum('amount'),
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
    public function update(Request $request, FeeType $feeType): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:fee_types,name,' . $feeType->id],
            'amount'      => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'frequency'   => ['required', 'in:monthly,quarterly,yearly,one_time'],
            'is_active'   => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

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