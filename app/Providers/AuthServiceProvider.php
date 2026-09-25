<?php

namespace App\Providers;

use App\Models\KategoriPengeluaran;
use App\Models\LaporanKeuanganHistory;
use App\Models\Penerimaan;
use App\Models\Pengeluaran;
use App\Models\Products;
use App\Models\Transactions;
use App\Models\User;
use App\Policies\KategoriPengeluaranPolicy;
use App\Policies\LaporanKeuanganHistoryPolicy;
use App\Policies\PenerimaanPolicy;
use App\Policies\PengeluaranPolicy;
use App\Policies\ProductsPolicy;
use App\Policies\TransactionsPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Products::class => ProductsPolicy::class,
        Transactions::class => TransactionsPolicy::class,
        Penerimaan::class => PenerimaanPolicy::class,
        Pengeluaran::class => PengeluaranPolicy::class,
        KategoriPengeluaran::class => KategoriPengeluaranPolicy::class,
        LaporanKeuanganHistory::class => LaporanKeuanganHistoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
