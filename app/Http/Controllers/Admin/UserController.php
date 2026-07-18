<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $query = User::query()
            ->with('roles')
            ->latest();

        // Recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre par rôle
        if ($request->filled('role')) {
            $query->role($request->input('role'));
        }

        // Filtre par statut
        if ($request->filled('status')) {
            match ($request->input('status')) {
                'verified'   => $query->whereNotNull('email_verified_at'),
                'unverified' => $query->whereNull('email_verified_at'),
                default      => null,
            };
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        $stats = [
            'total'      => User::count(),
            'admins'     => User::role('Admin')->count(),
            'teachers'   => User::role('Teacher')->count(),
            'students'   => User::role('Student')->count(),
            'parents'    => User::role('Parent')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
        ];

        return view('users.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $user  = new User();
        $roles = Role::orderBy('name')->get();

        return view('users.form', compact('user', 'roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', Password::defaults(), 'confirmed'],
            'role'     => ['required', 'string', 'exists:roles,name'],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required'     => 'Le nom est obligatoire.',
            'email.required'    => 'L\'email est obligatoire.',
            'email.unique'      => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed'=> 'La confirmation du mot de passe ne correspond pas.',
            'role.required'     => 'Le rôle est obligatoire.',
        ]);

        DB::beginTransaction();

        try {
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')
                    ->store('avatars/users', 'public');
            }

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'avatar'   => $avatarPath,
            ]);

            $user->assignRole($validated['role']);

            DB::commit();

            return to_route('users.index')
                ->with('success', "L'utilisateur « {$user->name} » a été créé avec succès.");

        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }

            return back()->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->load([
            'roles',
            'notifications' => fn($q) => $q->latest()->limit(10),
        ]);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $user->load('roles');
        $roles = Role::orderBy('name')->get();

        return view('users.form', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'password' => ['nullable', Password::defaults(), 'confirmed'],
            'role'     => ['required', 'string', 'exists:roles,name'],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {
            $payload = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ];

            // Nouveau mot de passe
            if ($request->filled('password')) {
                $payload['password'] = Hash::make($validated['password']);
            }

            // Nouvel avatar
            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $payload['avatar'] = $request->file('avatar')
                    ->store('avatars/users', 'public');
            }

            $user->update($payload);

            // Synchroniser le rôle
            $user->syncRoles([$validated['role']]);

            DB::commit();

            return to_route('users.index')
                ->with('success', "L'utilisateur « {$user->name} » a été mis à jour avec succès.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Protection : ne pas se supprimer soi-même
        if ($user->id === auth()->id()) {
            return to_route('users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Protection : ne pas supprimer le dernier admin
        if ($user->hasRole('Admin') && User::role('Admin')->count() <= 1) {
            return to_route('users.index')
                ->with('error', 'Impossible de supprimer le dernier administrateur.');
        }

        DB::beginTransaction();

        try {
            $name = $user->name;

            // Supprimer l'avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();

            DB::commit();

            return to_route('users.index')
                ->with('success', "L'utilisateur « {$name} » a été supprimé avec succès.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return to_route('users.index')
                ->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }
}