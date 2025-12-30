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
        if (!Schema::hasTable('loans')) {
            Schema::create('loans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('asset_id');
                $table->dateTime('tanggal_pinjam');
                $table->dateTime('tanggal_kembali')->nullable();
                $table->enum('status', ['Dipinjam', 'Dikembalikan'])->default('Dipinjam');
                $table->string('bukti_kembali')->nullable();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
