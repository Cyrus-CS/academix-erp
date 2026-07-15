<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\TeacherContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
// use Illuminate\Http\Request;

class TeacherContractController extends Controller
{
    public function index(Request $request): View
    {
        $query = TeacherContract::query()
            ->with('teacher.user')
            ->latest('start_date');

        // Filtres
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->input('teacher_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $contracts = $query->paginate(15)->withQueryString();
        $teachers  = Teacher::with('user')->orderBy('employee_number')->get();

        $stats = [
            'total'    => TeacherContract::count(),
            'active'   => TeacherContract::where('status', 'active')->count(),
            'expired'  => TeacherContract::where('status', 'expired')->count(),
            'expiring' => TeacherContract::where('status', 'active')
                ->where('end_date', '<=', now()->addDays(30))
                ->count(),
        ];

        return view('teacher-contracts.index', compact('contracts', 'teachers', 'stats'));
    }

    public function create(): View
    {
        $contract = new TeacherContract();
        $teachers = Teacher::with('user')
            ->where('status', 'active')
            ->orderBy('employee_number')
            ->get();

        return view('teacher-contracts.form', compact('contract', 'teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id'  => ['required', 'exists:teachers,id'],
            'type'        => ['required', 'in:permanent,temporary,part_time,internship'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['nullable', 'date', 'after:start_date'],
            'salary'      => ['required', 'numeric', 'min:0'],
            'status'      => ['required', 'in:active,expired,terminated'],
            'description' => ['nullable', 'string', 'max:1000'],
            'document'    => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ], [
            'teacher_id.required' => "L'enseignant est obligatoire.",
            'type.required'       => 'Le type de contrat est obligatoire.',
            'start_date.required' => 'La date de début est obligatoire.',
            'salary.required'     => 'Le salaire est obligatoire.',
            'end_date.after'      => 'La date de fin doit être après la date de début.',
            'document.mimes'      => 'Le document doit être un fichier PDF, DOC ou DOCX.',
            'document.max'        => 'Le document ne doit pas dépasser 5 Mo.',
        ]);

        // Upload document contrat
        if ($request->hasFile('document')) {
            $validated['document_path'] = $request->file('document')
                ->store('contracts', 'public');
        }

        $contract = TeacherContract::create($validated);

        return to_route('teacher-contracts.index')
            ->with('success', "Le contrat a été créé avec succès pour {$contract->teacher->user->name}.");
    }

    public function show(TeacherContract $teacherContract): View
    {
        $teacherContract->load('teacher.user');

        return view('teacher-contracts.show', compact('teacherContract'));
    }

    public function edit(TeacherContract $teacherContract): View
    {
        $teachers = Teacher::with('user')
            ->where('status', 'active')
            ->orderBy('employee_number')
            ->get();

        return view('teacher-contracts.form', compact('teacherContract', 'teachers'));
    }

    public function update(Request $request, TeacherContract $teacherContract): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id'  => ['required', 'exists:teachers,id'],
            'type'        => ['required', 'in:permanent,temporary,part_time,internship'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['nullable', 'date', 'after:start_date'],
            'salary'      => ['required', 'numeric', 'min:0'],
            'status'      => ['required', 'in:active,expired,terminated'],
            'description' => ['nullable', 'string', 'max:1000'],
            'document'    => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        // Nouveau document
        if ($request->hasFile('document')) {
            // Supprimer l'ancien
            if ($teacherContract->document_path) {
                Storage::disk('public')->delete($teacherContract->document_path);
            }
            $validated['document_path'] = $request->file('document')
                ->store('contracts', 'public');
        }

        $teacherContract->update($validated);

        return to_route('teacher-contracts.index')
            ->with('success', 'Le contrat a été mis à jour avec succès.');
    }

    public function destroy(TeacherContract $teacherContract): RedirectResponse
    {
        if ($teacherContract->status === 'active') {
            return to_route('teacher-contracts.index')
                ->with('error', 'Impossible de supprimer un contrat actif.');
        }

        // Supprimer le document
        if ($teacherContract->document_path) {
            Storage::disk('public')->delete($teacherContract->document_path);
        }

        $teacherContract->delete();

        return to_route('teacher-contracts.index')
            ->with('success', 'Le contrat a été supprimé avec succès.');
    }
}