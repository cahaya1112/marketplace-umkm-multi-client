<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_custom', function (Blueprint $table) {
            $table->id('id_permintaan_custom');

            $table->unsignedBigInteger('id_item_pesanan')->unique();
            $table->text('spesifikasi_custom');
            $table->string('status_custom')->default('MENUNGGU');
            $table->decimal('harga_custom', 15, 2)->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->unsignedBigInteger('id_admin_umkm_respon')->nullable();

            $table->timestamp('tanggal_permintaan');
            $table->timestamp('tanggal_respon')->nullable();

            $table->index('id_item_pesanan');
            $table->index('id_admin_umkm_respon');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_custom');
    }
};
