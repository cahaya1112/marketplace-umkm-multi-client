<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm', function (Blueprint $table) {
            $table->increments('id_umkm');

            $table->unsignedInteger('id_pengguna')->unique();

            $table->string('nama', 255);
            $table->text('deskripsi')->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('id_wilayah_asal', 255)->nullable();
            $table->string('kontak', 255)->nullable();
            $table->string('gambar_logo', 255)->nullable();

            $table->enum('status', [
                'AKTIF',
                'NONAKTIF',
            ]);

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm');
    }
};