<?php

namespace App\Policies;

use App\Constants\UserMenuConstant;
use App\Models\Karyawan;
use App\Models\User;

class KaryawanPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->authorize($user);
    }

    public function view(User $user, Karyawan $karyawan): bool
    {
        return $this->authorize($user);
    }

    public function create(User $user): bool
    {
        return $this->authorize($user);
    }

    public function update(User $user, Karyawan $karyawan): bool
    {
        return $this->authorize($user);
    }

    public function delete(User $user, Karyawan $karyawan): bool
    {
        return $this->authorize($user);
    }

    public function restore(User $user, Karyawan $karyawan): bool
    {
        return $this->authorize($user);
    }

    public function forceDelete(User $user, Karyawan $karyawan): bool
    {
        return $this->authorize($user);
    }

    protected function authorize(User $user): bool
    {
        return $user->menus->contains(key: 'name', value: UserMenuConstant::MENU_KARYAWAN);
    }
}
