<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of roles with their permissions.
     */
    public function index(Request $request): View
    {
        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $allPermissions = Permission::orderBy('group_name')
            ->orderBy('name')
            ->get()
            ->groupBy('group_name');

        $stats = [
            'total_roles'       => Role::count(),
            'total_permissions' => Permission::count(),
            'total_users'       => \App\Models\User::count(),
        ];

        return view('roles.index', compact('roles', 'allPermissions', 'stats'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $role = new Role();

        $allPermissions = Permission::orderBy('group_name')
            ->orderBy('name')
            ->get()
            ->groupBy('group_name');

        return view('roles.form', compact('role', 'allPermissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:50', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ], [
            'name.required' => 'Le nom du rôle est obligatoire.',
            'name.unique'   => 'Un rôle avec ce nom existe déjà.',
        ]);

        DB::beginTransaction();

        try {
            $role = Role::create(['name' => $validated['name']]);

            if (!empty($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            DB::commit();

            return to_route('roles.index')
                ->with('success', "Le rôle « {$role->name} » a été créé avec succès.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): View
    {
        $role->load([
            'permissions' => fn($q) => $q->orderBy('group_name')->orderBy('name'),
            'users',
        ]);

        $allPermissions = Permission::orderBy('group_name')
            ->orderBy('name')
            ->get()
            ->groupBy('group_name');

        return view('roles.show', compact('role', 'allPermissions'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        $role->load('permissions');

        $allPermissions = Permission::orderBy('group_name')
            ->orderBy('name')
            ->get()
            ->groupBy('group_name');

        return view('roles.form', compact('role', 'allPermissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:50', 'unique:roles,name,' . $role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        DB::beginTransaction();

        try {
            $role->update(['name' => $validated['name']]);
            $role->syncPermissions($validated['permissions'] ?? []);

            DB::commit();

            return to_route('roles.index')
                ->with('success', "Le rôle « {$role->name} » a été mis à jour avec succès.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // Protection : ne pas supprimer les rôles système
        $protectedRoles = ['Admin', 'Teacher', 'Student', 'Parent'];

        if (in_array($role->name, $protectedRoles)) {
            return to_route('roles.index')
                ->with('error', "Le rôle « {$role->name} » est un rôle système et ne peut pas être supprimé.");
        }

        if ($role->users()->exists()) {
            return to_route('roles.index')
                ->with('error', "Impossible de supprimer « {$role->name} » : des utilisateurs ont ce rôle.");
        }

        $name = $role->name;
        $role->delete();

        return to_route('roles.index')
            ->with('success', "Le rôle « {$name} » a été supprimé avec succès.");
    }
}