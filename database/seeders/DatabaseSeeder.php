<?php

namespace Database\Seeders;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Constants\UserMenuConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use const App\Constants\MENU_PRODUCT_MANAGEMENT;
use const App\Constants\MENU_TENANT_MANAGEMENT;
use const App\Constants\MENU_TRANSACTION_PAYMENT;
use const App\Constants\MENU_TRANSACTION;
use const App\Constants\MENU_USER_MANAGEMENT;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('user_menus')->insert([
            ['name' => UserMenuConstant::MENU_USER_MANAGEMENT],
            ['name' => UserMenuConstant::MENU_PRODUCT_MANAGEMENT],
            ['name' => UserMenuConstant::MENU_TRANSACTION],
            ['name' => UserMenuConstant::MENU_WIFI_TRANSACTION],
        ]);

    }
}
