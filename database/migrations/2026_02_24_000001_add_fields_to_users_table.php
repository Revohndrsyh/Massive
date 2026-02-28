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
        Schema::table('users', function (Blueprint $table) {
            // additional columns required by the design in the attached picture
            if (!Schema::hasColumn('users', 'user_id')) {
                $table->string('user_id', 10)->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'no_telp')) {
                $table->string('no_telp')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'nama_usaha')) {
                $table->string('nama_usaha', 25)->nullable()->after('no_telp');
            }
            if (!Schema::hasColumn('users', 'kategori_usaha')) {
                $table->string('kategori_usaha', 50)->nullable()->after('nama_usaha');
            }
            // you can also rename "name" to "nama" if you want to keep language consistent
            // but the default "name" field already corresponds to "Nama" in the picture
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('kategori_usaha');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'no_telp', 'nama_usaha', 'kategori_usaha', 'avatar']);
        });
    }
};
