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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('banks')->cascadeOnDelete();
            $table->string('type');
            $table->string('account_number');
            $table->string('account_name')->default('Test Nama Pengguna');
            $table->unsignedInteger('amount');
            $table->string('status')->default('Pending');
            $table->unsignedInteger('current')->nullable();
            $table->unsignedInteger('add')->nullable();
            $table->unsignedInteger('final')->nullable();
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
