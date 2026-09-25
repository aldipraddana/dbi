<?php

namespace App\Policies;

use App\Constants\UserMenuConstant;
use App\Models\Pengeluaran;
use App\Models\User;

class PengeluaranPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->authorize($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pengeluaran $pengeluaran): bool
    {
        return $this->authorize($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->authorize($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pengeluaran $pengeluaran): bool
    {
        return $this->authorize($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pengeluaran $pengeluaran): bool
    {
        return $this->authorize($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pengeluaran $pengeluaran): bool
    {
        return $this->authorize($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pengeluaran $pengeluaran): bool
    {
        return $this->authorize($user);
    }

    /**
     * Determine if the user have permission to access Pengeluaran.
     */
    protected function authorize(User $user): bool
    {
        return $user->menus->contains(key: 'name', value: UserMenuConstant::MENU_EXPENSE);
    }
}
