<?php

namespace App\Policies;

use App\Constants\UserMenuConstant;
use App\Models\DanaKeluar;
use App\Models\User;

class DanaKeluarPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->authorize($user);
    }

    public function view(User $user, DanaKeluar $danaKeluar): bool
    {
        return $this->authorize($user);
    }

    public function create(User $user): bool
    {
        return $this->authorize($user);
    }

    public function update(User $user, DanaKeluar $danaKeluar): bool
    {
        return $this->authorize($user);
    }

    public function delete(User $user, DanaKeluar $danaKeluar): bool
    {
        return $this->authorize($user);
    }

    public function restore(User $user, DanaKeluar $danaKeluar): bool
    {
        return $this->authorize($user);
    }

    public function forceDelete(User $user, DanaKeluar $danaKeluar): bool
    {
        return $this->authorize($user);
    }

    protected function authorize(User $user): bool
    {
        return $user->menus->contains('name', UserMenuConstant::MENU_DANA_KELUAR);
    }
}
