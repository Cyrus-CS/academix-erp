<?php

namespace App\Policies;

use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportCardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReportCard $reportCard): Response
    {
        return $user->hasAnyRole(['Admin', 'Teacher']) ? Response::allow() : Response::deny() ;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ReportCard $reportCard): bool
    {
        return $user->hasAnyRole(['Admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReportCard $reportCard): bool
    {
        return $user->hasAnyRole(['Admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ReportCard $reportCard): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ReportCard $reportCard): bool
    {
        return false;
    }
}