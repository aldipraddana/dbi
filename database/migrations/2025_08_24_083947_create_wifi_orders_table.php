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
        Schema::create('wifi_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('wifi_customer_id')->unique();
            $table->decimal('bill', 10, 2)->default(0)->nullable();
            $table->decimal('ppn', 10, 2)->default(0)->nullable();
            $table->decimal('total', 10, 2)->default(0)->nullable();
            $table->string('payment_type')->nullable();
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
        Schema::dropIfExists('wifi_orders');
    }
};
