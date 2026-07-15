<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherContract;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers.
     */
    public function index(Request $request): View
    {
        $activeYear = AcademicYear::active()->first();

        $query = Teacher::query()
            ->with(['user', 'contracts', 'assignments.subject'])
            ->withCount(['assignments', 'grades', 'contracts'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($sub) =>
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                )->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('qualification')) {
            $query->where('qualification', $request->input('qualification'));
        }

        $teachers = $query->paginate(15)->withQueryString();

        $totalTeachers   = Teacher::count();
        $activeContracts = TeacherContract::whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();
        $subjects        = Subject::orderBy('name')->get();
        $newThisMonth    = Teacher::newTeachersInMonth();

        return view('teachers.index', compact(
            'teachers', 'totalTeachers', 'activeContracts',
            'subjects', 'newThisMonth', 'activeYear'
        ));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create(): View
    {
        $teacher = new Teacher();

        return view('teachers.form', compact('teacher'));
    }

    /**
     * Store a newly created teacher.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Compte utilisateur
            'name'                  => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', Password::defaults(), 'confirmed'],
            'avatar'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // Informations enseignant
            'employee_number'       => ['required', 'string', 'max:50', 'unique:teachers,employee_number'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'nationality'           => ['nullable', 'string', 'max:100'],
            'qualification'         => ['required', 'string', 'max:150'],
            'specialization'        => ['nullable', 'string', 'max:150'],
            'date_of_birth'         => ['nullable', 'date', 'before:today'],
            'gender'                => ['required', 'in:male,female'],
            'address'               => ['nullable', 'string', 'max:255'],
            'hire_date'             => ['required', 'date'],
            'status'                => ['required', 'in:active,inactive,on_leave'],
            'bio'                   => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required'            => 'Le nom complet est obligatoire.',
            'email.required'           => "L'email est obligatoire.",
            'email.unique'             => 'Cet email est déjà utilisé.',
            'password.required'        => 'Le mot de passe est obligatoire.',
            'password.confirmed'       => 'La confirmation du mot de passe ne correspond pas.',
            'employee_number.required' => 'Le numéro employé est obligatoire.',
            'employee_number.unique'   => 'Ce numéro employé est déjà utilisé.',
            'qualification.required'   => 'La qualification est obligatoire.',
            'gender.required'          => 'Le genre est obligatoire.',
            'hire_date.required'       => "La date d'embauche est obligatoire.",
            'status.required'          => 'Le statut est obligatoire.',
        ]);

        DB::beginTransaction();

        try {
            // Upload avatar
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')
                    ->store('avatars/teachers', 'public');
            }

            // Créer l'utilisateur
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'avatar'   => $avatarPath,
            ]);

            // Assigner le rôle Teacher
            $user->assignRole('Teacher');

            // Créer le profil enseignant
            $teacher = Teacher::create([
                'user_id'         => $user->id,
                'employee_number' => $validated['employee_number'],
                'phone'           => $validated['phone'] ?? null,
                'nationality'     => $validated['nationality'] ?? null,
                'qualification'   => $validated['qualification'],
                'specialization'  => $validated['specialization'] ?? null,
                'date_of_birth'   => $validated['date_of_birth'] ?? null,
                'gender'          => $validated['gender'],
                'address'         => $validated['address'] ?? null,
                'hire_date'       => $validated['hire_date'],
                'status'          => $validated['status'],
                'bio'             => $validated['bio'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('teachers.show', $teacher)
                ->with('success', "L'enseignant « {$user->name} » a été créé avec succès.");

        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher): View
    {
        $teacher->load([
            'user',
            'teacherAssignments.subject',
            'teacherAssignments.schoolClass',
            'teacherAssignments.academicYear',
            'contracts' => fn($q) => $q->latest()->limit(5),
            'grades'    => fn($q) => $q->with('student.user', 'subject')->latest()->limit(10),
        ]);

        $stats = [
            'total_classes'      => $teacher->teacherAssignments()->distinct('school_class_id')->count(),
            'total_subjects'     => $teacher->teacherAssignments()->distinct('subject_id')->count(),
            'total_grades'       => $teacher->grades()->count(),
            'active_contract'    => $teacher->contracts()->where('status', 'active')->first(),
        ];

        return view('teachers.show', compact('teacher', 'stats'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('teachers.form', compact('teacher'));
    }

    /**
     * Update the specified teacher.
     */
    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $teacher->load('user');

        $validated = $request->validate([
            // Compte utilisateur
            'name'                  => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'unique:users,email,' . $teacher->user_id],
            'password'              => ['nullable', Password::defaults(), 'confirmed'],
            'avatar'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // Informations enseignant
            'employee_number'       => ['required', 'string', 'max:50', 'unique:teachers,employee_number,' . $teacher->id],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'nationality'           => ['nullable', 'string', 'max:100'],
            'qualification'         => ['required', 'string', 'max:150'],
            'specialization'        => ['nullable', 'string', 'max:150'],
            'date_of_birth'         => ['nullable', 'date', 'before:today'],
            'gender'                => ['required', 'in:male,female'],
            'address'               => ['nullable', 'string', 'max:255'],
            'hire_date'             => ['required', 'date'],
            'status'                => ['required', 'in:active,inactive,on_leave'],
            'bio'                   => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();

        try {
            // Mise à jour utilisateur
            $userPayload = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ];

            // Nouveau avatar
            if ($request->hasFile('avatar')) {
                // Supprimer l'ancien
                if ($teacher->user->avatar) {
                    Storage::disk('public')->delete($teacher->user->avatar);
                }
                $userPayload['avatar'] = $request->file('avatar')
                    ->store('avatars/teachers', 'public');
            }

            // Nouveau mot de passe (optionnel)
            if ($request->filled('password')) {
                $userPayload['password'] = Hash::make($validated['password']);
            }

            $teacher->user->update($userPayload);

            // Mise à jour profil enseignant
            $teacher->update([
                'employee_number' => $validated['employee_number'],
                'phone'           => $validated['phone'] ?? null,
                'nationality'     => $validated['nationality'] ?? null,
                'qualification'   => $validated['qualification'],
                'specialization'  => $validated['specialization'] ?? null,
                'date_of_birth'   => $validated['date_of_birth'] ?? null,
                'gender'          => $validated['gender'],
                'address'         => $validated['address'] ?? null,
                'hire_date'       => $validated['hire_date'],
                'status'          => $validated['status'],
                'bio'             => $validated['bio'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('teachers.show', $teacher)
                ->with('success', "Le profil de « {$teacher->user->name} » a été mis à jour avec succès.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->load('user');

        // Vérifier les dépendances
        if ($teacher->grades()->exists()) {
            return redirect()
                ->route('teachers.index')
                ->with('error', "Impossible de supprimer : des notes sont associées à cet enseignant.");
        }

        if ($teacher->contracts()->where('status', 'active')->exists()) {
            return redirect()
                ->route('teachers.index')
                ->with('error', "Impossible de supprimer : l'enseignant a un contrat actif.");
        }

        DB::beginTransaction();

        try {
            $name = $teacher->user->name;

            // Supprimer l'avatar
            if ($teacher->user->avatar) {
                Storage::disk('public')->delete($teacher->user->avatar);
            }

            $teacher->delete();
            $teacher->user->delete();

            DB::commit();

            return redirect()
                ->route('teachers.index')
                ->with('success', "L'enseignant « {$name} » a été supprimé avec succès.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route('teachers.index')
                ->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }
}