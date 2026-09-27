<?php

namespace App\Policies;

use App\Constants\UserMenuConstant;
use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->authorize($user);
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $this->authorize($user);
    }

    public function create(User $user): bool
    {
        return $this->authorize($user);
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $this->authorize($user);
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $this->authorize($user);
    }

    public function restore(User $user, Supplier $supplier): bool
    {
        return $this->authorize($user);
    }

    public function forceDelete(User $user, Supplier $supplier): bool
    {
        return $this->authorize($user);
    }

    protected function authorize(User $user): bool
    {
        return $user->menus->contains(key: 'name', value: UserMenuConstant::MENU_SUPPLIER);
    }
}
