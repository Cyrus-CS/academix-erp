<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parent\ParentRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ParentController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::role('Parent')
            ->with(['students.classe', 'students.user'])
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
        $students = Student::with(['user', 'classe'])->orderBy('matricule')->get();

        $stats = [
            'total'              => User::role('Parent')->count(),
            'with_students'      => User::role('Parent')->whereHas('students')->count(),
            'without_students'   => User::role('Parent')->whereDoesntHave('students')->count(),
            'total_associations' => DB::table('parents_users')->count(),
        ];

        return view('parents.index', compact('parents', 'students', 'stats'));
    }

    public function create(): View
    {
        $parent   = new User(); // ← User, pas ParentUser
        $students = Student::with(['user', 'classe'])
            ->orderBy('matricule')
            ->get();

        return view('parents.form', compact('parent', 'students'));
    }

    public function store(ParentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'phone'    => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password'] ?? \Str::random(12)),
            ]);

            $user->assignRole('Parent');

            // sync() sur la relation BelongsToMany User→students
            if (!empty($validated['student_ids'])) {
                $user->students()->sync($validated['student_ids']);
            }
        });

        return to_route('parents.index')
            ->with('success', 'Le parent a été créé avec succès.');
    }

    public function show(User $parent): View
    {
        abort_unless($parent->hasRole('Parent'), 404);

        $parent->load(['students.classe', 'students.user']);

        return view('parents.show', compact('parent'));
    }

    public function edit(User $parent): View
    {
        abort_unless($parent->hasRole('Parent'), 404);

        $parent->load('students');

        $students = Student::with(['user', 'classe'])
            ->orderBy('matricule')
            ->get();

        return view('parents.form', compact('parent', 'students'));
    }

    public function update(ParentRequest $request, User $parent): RedirectResponse
    {
        abort_unless($parent->hasRole('Parent'), 404);

        $validated = $request->validated();

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

            // sync() gère ajout ET suppression des associations
            $parent->students()->sync($validated['student_ids'] ?? []);
        });

        return to_route('parents.index')
            ->with('success', 'Le parent a été mis à jour avec succès.');
    }

    public function destroy(User $parent): RedirectResponse
    {
        abort_unless($parent->hasRole('Parent'), 404);

        DB::transaction(function () use ($parent) {
            $parent->students()->detach();
            $parent->delete();
        });

        return to_route('parents.index')
            ->with('success', 'Le parent a été supprimé avec succès.');
    }
}