<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\DanaKeluar;
use App\Models\KategoriPengeluaran;
use App\Models\Karyawan;
use App\Models\Kendaraan;
use App\Models\LaporanKeuanganHistory;
use App\Models\Penerimaan;
use App\Models\Pengeluaran;
use App\Models\PengeluaranStock;
use App\Models\Products;
use App\Models\Supplier;
use App\Models\Transactions;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Policies\DanaKeluarPolicy;
use App\Policies\KategoriPengeluaranPolicy;
use App\Policies\KaryawanPolicy;
use App\Policies\KendaraanPolicy;
use App\Policies\LaporanKeuanganHistoryPolicy;
use App\Policies\PenerimaanPolicy;
use App\Policies\PengeluaranPolicy;
use App\Policies\PengeluaranStockPolicy;
use App\Policies\ProductsPolicy;
use App\Policies\SupplierPolicy;
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
        PengeluaranStock::class => PengeluaranStockPolicy::class,
        Client::class => ClientPolicy::class,
        DanaKeluar::class => DanaKeluarPolicy::class,
        Supplier::class => SupplierPolicy::class,
        Karyawan::class => KaryawanPolicy::class,
        Kendaraan::class => KendaraanPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
