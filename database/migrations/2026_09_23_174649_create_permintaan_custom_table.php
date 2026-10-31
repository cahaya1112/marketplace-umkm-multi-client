<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_custom', function (Blueprint $table) {
            // Primary key sesuai SQL.
            $table->increments('id_custom');

            // Kolom relasi sesuai struktur SQL.
            // Foreign key akan dipasang setelah migration tabel
            // yang menjadi tujuan relasi tersedia.
            $table->unsignedInteger('id_pengguna');
            $table->unsignedInteger('id_produk');
            $table->unsignedInteger('id_keranjang_item');

            $table->text('spesifikasi');
            $table->integer('harga_custom')->nullable();

            $table->enum('status_custom', [
                'PENDING',
                'DISETUJUI',
                'DITOLAK',
            ]);

            $table->text('catatan_umkm')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_custom');
    }
};
