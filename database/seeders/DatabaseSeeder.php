<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Constants\UserMenuConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('user_menus')->insert([
            ['name' => UserMenuConstant::MENU_USER_MANAGEMENT],
            ['name' => UserMenuConstant::MENU_PENERIMAAN],
            ['name' => UserMenuConstant::MENU_EXPENSE],
            ['name' => UserMenuConstant::MENU_LAPORAN_KEUANGAN]
        ]);

        $this->call([
            KategoriPengeluaranSeeder::class,
        ]);
    }
}
