<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM(
            'superadmin','admin','teknisi','teknisi_cctv'
        ) NOT NULL DEFAULT 'teknisi'");

        Schema::table('jobs', function (Blueprint $table) {
            $table->enum('bidang', ['komputer', 'cctv'])
                  ->default('komputer')
                  ->after('kategori');
            $table->index('bidang');
        });

        DB::table('jobs')->update(['bidang' => 'komputer']);
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex(['bidang']);
            $table->dropColumn('bidang');
        });

        DB::statement("ALTER TABLE users MODIFY role ENUM(
            'superadmin','admin','teknisi'
        ) NOT NULL DEFAULT 'teknisi'");
    }
};