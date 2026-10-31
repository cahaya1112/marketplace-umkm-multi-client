<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Indeks unik stok.id_produk sudah didefinisikan
        // pada migration create_remaining_marketplace_tables.
        // Tidak perlu dibuat ulang di sini.

        Schema::table('keranjang', function (Blueprint $table) {
            $table->unique(
                'id_pengguna',
                'keranjang_id_pengguna_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('keranjang', function (Blueprint $table) {
            $table->dropUnique('keranjang_id_pengguna_unique');
        });
    }
};