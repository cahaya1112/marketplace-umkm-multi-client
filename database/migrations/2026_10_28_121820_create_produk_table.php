<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->increments('id_produk');

            $table->unsignedInteger('id_umkm');
            $table->unsignedInteger('id_kategori');

            $table->string('nama', 255);
            $table->text('deskripsi')->nullable();
            $table->integer('harga_retail');
            $table->integer('berat_gram');
            $table->string('gambar', 255);

            $table->foreign('id_umkm')
                ->references('id_umkm')
                ->on('umkm');

            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
