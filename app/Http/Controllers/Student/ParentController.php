<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ParentController extends Controller
{
    /**
     * Liste des parents avec leurs élèves associés.
     */
    public function index(Request $request): View
    {
        $query = User::role('Parent')
            ->with(['students.classe'])
            ->withCount('students');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $parents  = $query->latest()->paginate(15)->withQueryString();
        $students = Student::with(['user', 'classe'])
            ->orderBy('matricule')
            ->get();

        $stats = [
            'total'              => User::role('Parent')->count(),
            'with_students'      => User::role('Parent')
                ->whereHas('students')
                ->count(),
            'without_students'   => User::role('Parent')
                ->whereDoesntHave('students')
                ->count(),
            'total_associations' => DB::table('parents_users')->count(),
        ];

        return view('parents.index', compact('parents', 'students', 'stats'));
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        $parent   = new User();
        $students = Student::with(['user', 'classe'])
            ->orderBy('matricule')
            ->get();

        return view('parents.form', compact('parent', 'students'));
    }

    /**
     * Enregistrer un nouveau parent.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'password'    => ['nullable', 'string', 'min:8'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
        ], [
            'name.required'  => 'Le nom est obligatoire.',
            'email.required' => "L'email est obligatoire.",
            'email.unique'   => 'Cet email est déjà utilisé.',
        ]);

        DB::transaction(function () use ($validated) {
            // Créer l'utilisateur avec le rôle Parent
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'phone'    => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password'] ?? \Str::random(12)),
            ]);

            $user->assignRole('Parent');

            // Associer les élèves
            if (!empty($validated['student_ids'])) {
                $user->students()->sync($validated['student_ids']);
            }
        });

        return to_route('parents.index')
            ->with('success', 'Le parent a été créé et ses élèves associés avec succès.');
    }

    /**
     * Détail d'un parent.
     */
    public function show(User $parent): View
    {
        abort_unless($parent->hasRole('Parent'), 404);

        $parent->load(['students.classe', 'students.user']);

        return view('parents.show', compact('parent'));
    }

    /**
     * Formulaire de modification.
     */
    public function edit(User $parent): View
    {
        abort_unless($parent->hasRole('Parent'), 404);

        $parent->load('students');

        $students = Student::with(['user', 'classe'])
            ->orderBy('matricule')
            ->get();

        return view('parents.form', compact('parent', 'students'));
    }

    /**
     * Mettre à jour un parent et ses associations.
     */
    public function update(Request $request, User $parent): RedirectResponse
    {
        abort_unless($parent->hasRole('Parent'), 404);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', "unique:users,email,{$parent->id}"],
            'phone'         => ['nullable', 'string', 'max:20'],
            'password'      => ['nullable', 'string', 'min:8'],
            'student_ids'   => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
        ]);

        DB::transaction(function () use ($validated, $parent) {
            $updateData = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $parent->update($updateData);

            // sync() gère l'ajout ET la suppression des associations
            $parent->students()->sync($validated['student_ids'] ?? []);
        });

        return to_route('parents.index')
            ->with('success', 'Le parent a été mis à jour avec succès.');
    }

    /**
     * Supprimer un parent.
     */
    public function destroy(User $parent): RedirectResponse
    {
        abort_unless($parent->hasRole('Parent'), 404);

        DB::transaction(function () use ($parent) {
            // Détacher tous les élèves (supprime les lignes de la pivot)
            $parent->students()->detach();
            $parent->delete();
        });

        return to_route('parents.index')
            ->with('success', 'Le parent a été supprimé avec succès.');
    }

    /**
     * AJAX — Associer / désassocier un élève à un parent.
     */
    public function toggleStudent(Request $request, User $parent): \Illuminate\Http\JsonResponse
    {
        abort_unless($parent->hasRole('Parent'), 404);

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'attach'     => ['required', 'boolean'],
        ]);

        if ($validated['attach']) {
            // Évite les doublons grâce à syncWithoutDetaching
            $parent->students()->syncWithoutDetaching([$validated['student_id']]);
            $message = 'Élève associé avec succès.';
        } else {
            $parent->students()->detach($validated['student_id']);
            $message = 'Élève dissocié avec succès.';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }
}