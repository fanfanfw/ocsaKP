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
        if (!Schema::hasTable('asset_parts')) {
            Schema::create('asset_parts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('asset_id');
                $table->string('nama_part', 100);
                $table->string('kondisi', 50)->nullable();
                $table->integer('jumlah')->default(1);
                $table->text('keterangan')->nullable();

                $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_parts');
    }
};
