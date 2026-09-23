<?php

use Illuminate\Database\Console\Migrations\StatusCommand;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->nullable();
            $table->date('transaction_date')->nullable();
            $table->string('customer')->comment('Nama pelanggan');
            $table->string('no_handphone')->comment('No Handphone pelanggan');
            $table->string('address')->comment('Alamat pelanggan');
            $table->decimal('total', 10, 2)->default(0)->nullable();
            $table->string('status')->default('pending')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
