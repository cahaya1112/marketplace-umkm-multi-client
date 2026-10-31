<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table): void {
            // Hapus kolom role bawaan agar tidak bentrok
            $table->dropColumn('role');
            
            // Tambahkan kolom baru sesuai SRS
            $table->enum('peran', ['SUPER_ADMIN', 'ADMIN_UMKM', 'PEMBELI'])->default('PEMBELI')->after('password');
            $table->enum('status', ['AKTIF', 'NONAKTIF'])->default('AKTIF')->after('peran');
            $table->string('nomor_telepon')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table): void {
            $table->dropColumn(['peran', 'status', 'nomor_telepon']);
            $table->enum('role', ['admin', 'umkm_owner', 'customer'])->default('customer');
        });
    }
};