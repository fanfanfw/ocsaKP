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
        if (!Schema::hasTable('jadwal_otomatis')) {
            Schema::create('jadwal_otomatis', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('asset_id')->nullable();
                $table->date('tanggal')->nullable();
                $table->time('jam_mulai')->nullable();
                $table->time('jam_selesai')->nullable();
                $table->enum('status', ['Terjadwal', 'Berjalan', 'Selesai'])->default('Terjadwal');

                $table->foreign('asset_id')->references('id')->on('assets');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_otomatis');
    }
};
