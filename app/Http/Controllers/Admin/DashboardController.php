<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Dashboard\AdminDashboardService;
use App\Services\Dashboard\ParentDashboardService;
use App\Services\Dashboard\StudentDashboardService;
use App\Services\Dashboard\TeacherDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AdminDashboardService $adminDashboard,
        private readonly StudentDashboardService $studentDashboard,
        private readonly TeacherDashboardService $teacherDashboard,
        private readonly ParentDashboardService $parentDashboard,
    ) {}

    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return match (true) {
            $user->isAdministrator() => view('dashboard.admin', $this->adminDashboard->build()),
            $user->isStudent() => $this->studentView($user),
            $user->isTeacher() => $this->teacherView($user),
            $user->isParent() => $this->parentView($user),
            default => abort(403, 'Role inconnu'),
        };
    }

    private function studentView(User $user): View
    {
        $student = $user->student;

        if (! $student) {
            abort(404, 'Profil étudiant introuvable.');
        }

        return view('dashboard.student', $this->studentDashboard->build($student));
    }

    private function teacherView(User $user): View
    {
        $teacher = $user->teacher;

        if (! $teacher) {
            abort(404, 'Profil enseignant introuvable.');
        }

        return view('dashboard.teacher', $this->teacherDashboard->build($teacher));
    }

    private function parentView(User $user): View
    {
        $parent = $user->parent;

        if (! $parent) {
            abort(404, 'Profil parent introuvable.');
        }

        return view('dashboard.parent', $this->parentDashboard->build($parent));
    }

    /**
     * AJAX : rafraîchir les stats (utilisé par le polling Chart.js).
     */
    public function refreshStats(Request $request): JsonResponse
    {
        if (! Auth::user()->hasRole('Administrateur')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        AdminDashboardService::clearCache();

        return response()->json([
            'message' => 'Cache rafraîchi.',
            'updated_at' => now()->format('H:i:s'),
        ]);
    }
}