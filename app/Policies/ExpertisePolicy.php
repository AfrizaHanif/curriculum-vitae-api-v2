<?php

namespace App\Policies;

use App\Models\Expertise;
use App\Models\User;

class ExpertisePolicy
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
    public function view(?User $user, Expertise $expertise): bool
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
    public function update(User $user, Expertise $expertise): bool
    {
        return $user->profile?->id !== null && $expertise->profile_id === $user->profile->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expertise $expertise): bool
    {
        return $user->profile?->id !== null && $expertise->profile_id === $user->profile->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expertise $expertise): bool
    {
        return $user->profile?->id !== null && $expertise->profile_id === $user->profile->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expertise $expertise): bool
    {
        return $user->profile?->id !== null && $expertise->profile_id === $user->profile->id;
    }
}
