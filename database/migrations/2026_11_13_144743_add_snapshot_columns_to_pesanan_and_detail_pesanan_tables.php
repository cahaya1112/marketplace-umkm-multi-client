<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('nama_penerima_snapshot')->nullable();
            $table->string('nomor_telepon_snapshot')->nullable();
            $table->text('alamat_lengkap_snapshot')->nullable();
            $table->string('id_wilayah_tujuan_snapshot')->nullable();
            $table->string('kode_pos_snapshot')->nullable();
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->string('nama_produk_snapshot')->nullable();
            $table->text('spesifikasi_custom_snapshot')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropColumn([
                'nama_produk_snapshot',
                'spesifikasi_custom_snapshot',
            ]);
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn([
                'nama_penerima_snapshot',
                'nomor_telepon_snapshot',
                'alamat_lengkap_snapshot',
                'id_wilayah_tujuan_snapshot',
                'kode_pos_snapshot',
            ]);
        });
    }
};
