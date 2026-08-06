<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileUserController extends Controller
{
    /**
     * Afficher la page de consultation du profil.
     */
    public function index(Request $request): View
    {
        return view('profile.index', [
            'user' => $request->user(),
    ]);
}


    /**
     * Afficher le formulaire de profil de l'utilisateur connecté.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Mettre à jour les informations du profil.
     */
    public function update(Request $request): RedirectResponse
{
    $user = $request->user();

    $validated = $request->validate([
        'name'   => ['required', 'string', 'max:255'],
        'email'  => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user->id),
        ],
        'phone'  => ['nullable', 'string', 'max:20'],
        'avatar' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
        ],
    ]);

    // ── Réinitialiser la vérification email si changement ────
    if ($user->email !== $validated['email']) {
        $user->email_verified_at = null;
    }

    // ── Mise à jour des champs texte ─────────────────────────
    $user->name  = $validated['name'];
    $user->email = $validated['email'];
    $user->phone = $validated['phone'] ?? null;

    // ── Mise à jour de l'avatar UNIQUEMENT si fichier uploadé ─
    if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
        
        // Supprimer l'ancien avatar si existant
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Stocker le nouveau et mettre à jour le champ
        $user->avatar = $request->file('avatar')->store('avatars', 'public');
    }

    // ── Sauvegarder en base ──────────────────────────────────
    $user->save();

    return to_route('profile.edit')
        ->with('success', 'Votre profil a été mis à jour avec succès.');
}

    /**
     * Mettre à jour le mot de passe.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ], [
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min'  => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return to_route('profile.edit')
            ->with('success', 'Votre mot de passe a été modifié avec succès.');
    }

    /**
     * Supprimer l'avatar uniquement.
     */
    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return to_route('profile.edit')
            ->with('success', 'Votre photo de profil a été supprimée.');
    }

    /**
     * Supprimer le compte de l'utilisateur.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ], [
            'password.current_password' => 'Le mot de passe saisi est incorrect.',
        ]);

        $user = $request->user();

        // Supprimer l'avatar si existant
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Déconnecter avant suppression
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login')
            ->with('success', 'Votre compte a été supprimé avec succès.');
    }
}