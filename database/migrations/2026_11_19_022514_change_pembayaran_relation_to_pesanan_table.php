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
        Schema::table('pembayaran', function (Blueprint $table) {
            // Hapus relasi lama ke sub_pesanan
            $table->dropForeign(
                'pembayaran_id_sub_pesanan_foreign'
            );

            $table->dropColumn('id_sub_pesanan');

            // Tambahkan relasi ke pesanan utama
            $table->unsignedInteger('id_pesanan')
                ->after('id_pembayaran');

            $table->foreign(
                'id_pesanan',
                'pembayaran_id_pesanan_foreign'
            )
                ->references('id_pesanan')
                ->on('pesanan')
                ->onDelete('cascade');

            // Satu pesanan hanya memiliki satu pembayaran
            $table->unique(
                'id_pesanan',
                'pembayaran_id_pesanan_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            // Hapus relasi baru
            $table->dropForeign(
                'pembayaran_id_pesanan_foreign'
            );

            $table->dropUnique(
                'pembayaran_id_pesanan_unique'
            );

            $table->dropColumn('id_pesanan');

            // Kembalikan relasi lama
            $table->unsignedInteger('id_sub_pesanan')
                ->after('id_pembayaran');

            $table->foreign(
                'id_sub_pesanan',
                'pembayaran_id_sub_pesanan_foreign'
            )
                ->references('id_sub_pesanan')
                ->on('sub_pesanan');
        });
    }
};
