<?php

namespace App\Policies;

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MonitorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view monitors in their organization
    }

    public function view(User $user, Monitor $monitor): bool
    {
        return $user->organization_id === $monitor->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Monitor $monitor): bool
    {
        return $user->organization_id === $monitor->organization_id && $user->isAdmin();
    }

    public function delete(User $user, Monitor $monitor): bool
    {
        return $user->organization_id === $monitor->organization_id && $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Monitor $monitor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Monitor $monitor): bool
    {
        return false;
    }
}
