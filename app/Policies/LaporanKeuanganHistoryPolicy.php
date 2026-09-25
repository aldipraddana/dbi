<?php

namespace App\Policies;

use App\Constants\UserMenuConstant;
use App\Models\LaporanKeuanganHistory;
use App\Models\User;

class LaporanKeuanganHistoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->authorize($user);
    }

    public function view(User $user, LaporanKeuanganHistory $laporanKeuanganHistory): bool
    {
        return $this->authorize($user);
    }

    public function create(User $user): bool
    {
        return $this->authorize($user);
    }

    public function update(User $user, LaporanKeuanganHistory $laporanKeuanganHistory): bool
    {
        return $this->authorize($user);
    }

    public function delete(User $user, LaporanKeuanganHistory $laporanKeuanganHistory): bool
    {
        return $this->authorize($user);
    }

    public function restore(User $user, LaporanKeuanganHistory $laporanKeuanganHistory): bool
    {
        return $this->authorize($user);
    }

    public function forceDelete(User $user, LaporanKeuanganHistory $laporanKeuanganHistory): bool
    {
        return $this->authorize($user);
    }

    protected function authorize(User $user): bool
    {
        return $user->menus->contains('name', UserMenuConstant::MENU_LAPORAN_KEUANGAN);
    }
}
