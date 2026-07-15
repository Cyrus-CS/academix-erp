<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'is_active' => true,
            'password'  => Hash::make($request->password),
        ]);

        // ── Assigner le rôle par défaut ──
        $user->assignRole('Student'); // ou selon votre logique

        // ── Déclencher l'event (envoi email vérification) ──
        try {
            event(new Registered($user));
        } catch (\Exception $e) {
            // L'inscription ne doit pas échouer si l'email ne part pas
            Log::error('Erreur envoi email vérification', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);

            // On connecte quand même l'utilisateur
            Auth::login($user);

            return redirect()
                ->route('dashboard')
                ->with('warning', 'Compte créé mais l\'email de vérification n\'a pas pu être envoyé. Vérifiez votre configuration mail.');
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}