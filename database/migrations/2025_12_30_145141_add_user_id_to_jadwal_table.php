<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('jadwal') && !Schema::hasColumn('jadwal', 'user_id')) {
            $column = DB::selectOne("SHOW COLUMNS FROM users WHERE Field = 'id'");
            $type = strtolower($column->Type ?? '');
            $isBigInt = str_contains($type, 'bigint');
            $isUnsigned = str_contains($type, 'unsigned');

            Schema::table('jadwal', function (Blueprint $table) use ($isBigInt, $isUnsigned) {
                $userId = $isBigInt
                    ? ($isUnsigned ? $table->unsignedBigInteger('user_id') : $table->bigInteger('user_id'))
                    : ($isUnsigned ? $table->unsignedInteger('user_id') : $table->integer('user_id'));
                $userId->nullable()->after('asset_id');
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('jadwal') && Schema::hasColumn('jadwal', 'user_id')) {
            Schema::table('jadwal', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
