<?php

namespace App\Policies;

use App\Constants\UserMenuConstant;
use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->authorize($user);
    }

    public function view(User $user, Client $client): bool
    {
        return $this->authorize($user);
    }

    public function create(User $user): bool
    {
        return $this->authorize($user);
    }

    public function update(User $user, Client $client): bool
    {
        return $this->authorize($user);
    }

    public function delete(User $user, Client $client): bool
    {
        return $this->authorize($user);
    }

    public function restore(User $user, Client $client): bool
    {
        return $this->authorize($user);
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return $this->authorize($user);
    }

    protected function authorize(User $user): bool
    {
        return $user->menus->contains(key: 'name', value: UserMenuConstant::MENU_CLIENT);
    }
}
