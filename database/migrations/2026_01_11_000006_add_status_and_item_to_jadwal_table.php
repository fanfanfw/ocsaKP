<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->enum('status', ['Terjadwal', 'Diterima', 'Selesai'])->default('Terjadwal')->after('keterangan');
            // We might need to track WHICH item was received.
            $table->foreignId('asset_item_id')->nullable()->after('asset_id')->constrained('asset_items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropForeign(['asset_item_id']);
            $table->dropColumn('asset_item_id');
            $table->dropColumn('status');
        });
    }
};
