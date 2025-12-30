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
        if (!Schema::hasTable('maintenance')) {
            Schema::create('maintenance', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('asset_id');
                $table->text('deskripsi');
                $table->date('tanggal');
                $table->string('part', 100)->nullable();
                $table->string('jenis_kerusakan', 100)->nullable();
                $table->enum('tingkat', ['Ringan', 'Sedang', 'Berat'])->nullable();
                $table->text('tindakan')->nullable();
                $table->date('tanggal_selesai')->nullable();

                $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance');
    }
};
