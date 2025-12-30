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
        if (!Schema::hasTable('asset_status_log')) {
            Schema::create('asset_status_log', function (Blueprint $table) {
                $table->unsignedBigInteger('asset_id')->nullable();
                $table->string('status', 50)->nullable();
                $table->dateTime('updated_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_status_log');
    }
};
