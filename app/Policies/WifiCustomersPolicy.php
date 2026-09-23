<?php

namespace App\Policies;

use App\Constants\UserMenuConstant;
use App\Models\User;
use App\Models\WifiCustomers;

class WifiCustomersPolicy
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
    public function view(User $user, WifiCustomers $wifiCustomers): bool
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
    public function update(User $user, WifiCustomers $wifiCustomers): bool
    {
        return $this->authorize($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WifiCustomers $wifiCustomers): bool
    {
        return $this->authorize($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WifiCustomers $wifiCustomers): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WifiCustomers $wifiCustomers): bool
    {
        return false;
    }

    /**
     * Determine if the user have permission to access cash.
     */
    protected function authorize(User $user): bool
    {
        return $user->menus->contains(key: 'name', value: UserMenuConstant::MENU_WIFI_CUSTOMERS);
    }
}
