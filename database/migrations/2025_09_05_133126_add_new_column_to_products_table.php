<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
             $table->string('imei1')->nullable()->after('serial_number');
             $table->string('imei2')->nullable()->after('imei1');
             $table->boolean('joki')->default(false)->after('description');
             $table->string('joki_name')->nullable()->after('joki');
             $table->decimal('joki_nominal', 10, 2)->default(0)->nullable()->after('joki_name');
             $table->text('marketplace')->nullable();
             $table->float('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
