<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Alamat pembeli
        Schema::create('alamat_pembeli', function (Blueprint $table) {
            $table->increments('id_alamat');
            $table->unsignedInteger('id_pengguna');
            $table->string('nama_penerima');
            $table->string('nomor_telepon');
            $table->string('alamat_lengkap');
            $table->string('id_wilayah_tujuan');
            $table->string('kode_pos');

            $table->foreign('id_pengguna')
                ->references('id_pengguna')->on('pengguna');
        });

        // 2. Stok produk
        Schema::create('stok', function (Blueprint $table) {
        $table->increments('id_stok');
        $table->unsignedInteger('id_produk')->unique();
        $table->integer('jumlah_stok');
        $table->integer('batas_minimum');

        $table->foreign('id_produk')
            ->references('id_produk')
            ->on('produk');
        });

        // 3. Keranjang
        Schema::create('keranjang', function (Blueprint $table) {
            $table->increments('id_keranjang');
            $table->unsignedInteger('id_pengguna');

            $table->foreign('id_pengguna')
                ->references('id_pengguna')->on('pengguna');
        });

        // 4. Item keranjang
        Schema::create('keranjang_item', function (Blueprint $table) {
            $table->increments('id_keranjang_item');
            $table->unsignedInteger('id_keranjang');
            $table->unsignedInteger('id_produk');
            $table->integer('jumlah');

            $table->foreign('id_keranjang')
                ->references('id_keranjang')->on('keranjang');

            $table->foreign('id_produk')
                ->references('id_produk')->on('produk');
        });

        // 5. Pesanan
        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id_pesanan');
            $table->unsignedInteger('id_pengguna');
            // $table->unsignedInteger('id_pengguna');
            $table->unsignedInteger('id_alamat');
            $table->string('kode_pesanan')->unique();
            $table->dateTime('tanggal_pesanan');

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna');
                

            $table->foreign('id_alamat')
                ->references('id_alamat')->on('alamat_pembeli');
        });

        // 6. Sub-pesanan
        Schema::create('sub_pesanan', function (Blueprint $table) {
            $table->increments('id_sub_pesanan');
            $table->unsignedInteger('id_pesanan');
            $table->unsignedInteger('id_umkm');

            $table->enum('status_sub_pesanan', [
                'MENUNGGU_PEMBAYARAN',
                'DIPROSES',
                'DIKIRIM',
                'SELESAI',
                'DIBATALKAN',
            ]);

            $table->string('kurir_kode')->nullable();
            $table->string('kurir_layanan')->nullable();
            $table->string('estimasi_pengiriman')->nullable();
            $table->integer('biaya_pengiriman')->nullable();
            $table->string('nomor_resi')->nullable();
            $table->dateTime('dibuat_pada');

            $table->foreign('id_pesanan')
                ->references('id_pesanan')->on('pesanan');

            $table->foreign('id_umkm')
                ->references('id_umkm')->on('umkm');
        });

        // 7. Detail pesanan
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->increments('id_detail_pesanan');
            $table->unsignedInteger('id_sub_pesanan');
            $table->unsignedInteger('id_produk');
            $table->unsignedInteger('id_custom')->nullable();
            $table->integer('harga_retail');
            $table->integer('harga_custom')->nullable();
            $table->integer('jumlah');

            $table->foreign('id_sub_pesanan')
                ->references('id_sub_pesanan')->on('sub_pesanan');

            $table->foreign('id_produk')
                ->references('id_produk')->on('produk');
        });

        // 8. Pembayaran
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id_pembayaran');
            $table->unsignedInteger('id_sub_pesanan');

            $table->string('order_id_gateway')->nullable()->unique();
            $table->string('id_transaksi_gateway')->nullable();

            // Jenis metode disimpan sebagai string karena
            // nilai enum metode_pembayaran belum ditentukan
            // dalam spesifikasi SQL yang diberikan.
            $table->string('metode_pembayaran');

            $table->integer('jumlah_bayar');
            $table->enum('status_pembayaran', [
                'PENDING',
                'PAID',
                'FAILED',
                'EXPIRED',
            ]);

            $table->string('token_pembayaran')->nullable();
            $table->json('payload_notifikasi')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->dateTime('waktu_bayar')->nullable();

            $table->foreign('id_sub_pesanan')
                ->references('id_sub_pesanan')->on('sub_pesanan');
        });

        // 9. Retur
        Schema::create('retur', function (Blueprint $table) {
            $table->increments('id_retur');
            $table->unsignedInteger('id_detail_pesanan');
            $table->integer('jumlah');
            $table->text('alasan');
            $table->string('jenis_retur');
            $table->string('status_retur');
            $table->string('bukti_gambar')->nullable();
            $table->string('kurir_retur')->nullable();
            $table->string('nomor_resi_retur')->nullable();
            $table->string('kurir_barang_pengganti')->nullable();
            $table->string('nomor_resi_barang_pengganti')->nullable();
            $table->text('catatan_umkm')->nullable();
            $table->dateTime('waktu_diajukan');

            $table->foreign('id_detail_pesanan')
                ->references('id_detail_pesanan')->on('detail_pesanan');
        });

        // 10. Pengembalian dana
        Schema::create('pengembalian_dana', function (Blueprint $table) {
            $table->increments('id_pengembalian');
            $table->unsignedInteger('id_retur');
            $table->unsignedInteger('id_pembayaran');
            $table->integer('jumlah_pengembalian');
            $table->text('alasan');
            $table->string('id_refund_gateway')->nullable();
            $table->string('status_pengembalian');
            $table->json('payload_respons')->nullable();
            $table->dateTime('waktu_pengembalian');

            $table->foreign('id_retur')
                ->references('id_retur')->on('retur');

            $table->foreign('id_pembayaran')
                ->references('id_pembayaran')->on('pembayaran');
        });

        // 11. Audit log
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id_audit');
            $table->unsignedInteger('id_pengguna');
            $table->string('peran')->nullable();
            $table->unsignedInteger('id_umkm')->nullable();
            $table->string('aksi');
            $table->string('nama_entitas');
            $table->unsignedInteger('id_entitas')->nullable();
            $table->json('data_sebelum')->nullable();
            $table->json('data_sesudah')->nullable();
            $table->string('ip_address')->nullable();
            $table->dateTime('dibuat_pada');

            $table->foreign('id_pengguna')
                ->references('id_pengguna')->on('pengguna');

            $table->foreign('id_umkm')
                ->references('id_umkm')->on('umkm');
        });

        // Tambahkan foreign key permintaan_custom setelah
        // produk dan keranjang_item tersedia.
        Schema::table('permintaan_custom', function (Blueprint $table) {
            $table->foreign('id_pengguna')
                ->references('id_pengguna')->on('pengguna');

            $table->foreign('id_produk')
                ->references('id_produk')->on('produk');

            $table->foreign('id_keranjang_item')
                ->references('id_keranjang_item')->on('keranjang_item');
        });

        // Relasi custom pada detail pesanan.
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->foreign('id_custom')
                ->references('id_custom')->on('permintaan_custom');
        });
    }

    public function down(): void
    {
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropForeign(['id_custom']);
        });

        Schema::table('permintaan_custom', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
            $table->dropForeign(['id_produk']);
            $table->dropForeign(['id_keranjang_item']);
        });

        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('pengembalian_dana');
        Schema::dropIfExists('retur');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('sub_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('keranjang_item');
        Schema::dropIfExists('keranjang');
        Schema::dropIfExists('stok');
        Schema::dropIfExists('alamat_pembeli');
    }
};