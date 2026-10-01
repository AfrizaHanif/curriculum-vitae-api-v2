<?php

namespace App\Policies;

use App\Models\CaseStudy;
use App\Models\User;

class CaseStudyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, CaseStudy $caseStudy): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->profile()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CaseStudy $caseStudy): bool
    {
        return $user->id === $caseStudy->portfolio?->profile?->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CaseStudy $caseStudy): bool
    {
        return $user->id === $caseStudy->portfolio?->profile?->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CaseStudy $caseStudy): bool
    {
        return $user->id === $caseStudy->portfolio?->profile?->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CaseStudy $caseStudy): bool
    {
        return $user->id === $caseStudy->portfolio?->profile?->user_id;
    }
}
