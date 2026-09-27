<?php

namespace App\Policies;

use App\Constants\UserMenuConstant;
use App\Models\Kendaraan;
use App\Models\User;

class KendaraanPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->authorize($user);
    }

    public function view(User $user, Kendaraan $kendaraan): bool
    {
        return $this->authorize($user);
    }

    public function create(User $user): bool
    {
        return $this->authorize($user);
    }

    public function update(User $user, Kendaraan $kendaraan): bool
    {
        return $this->authorize($user);
    }

    public function delete(User $user, Kendaraan $kendaraan): bool
    {
        return $this->authorize($user);
    }

    public function restore(User $user, Kendaraan $kendaraan): bool
    {
        return $this->authorize($user);
    }

    public function forceDelete(User $user, Kendaraan $kendaraan): bool
    {
        return $this->authorize($user);
    }

    protected function authorize(User $user): bool
    {
        return $user->menus->contains(key: 'name', value: UserMenuConstant::MENU_KENDARAAN);
    }
}
