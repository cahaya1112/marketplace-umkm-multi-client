@extends('layouts.masuk')
@section('title', 'Registrasi Pembeli | GintanganCraft')
@section('caption_title', 'Bergabung Bersama Pengrajin Lokal')
@section('caption_text', 'Buat akun pembeli untuk menjelajahi produk anyaman bambu, rotan, dan furnitur handmade dari UMKM terpercaya.')

@section('form')
<h1 class="font-serif text-2xl font-semibold">Registrasi Pembeli</h1>
<p class="mt-1 text-sm text-slate2">Buat akun baru untuk mulai berbelanja.</p>

<form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-4">
@csrf
<x-gi-field name="name" label="Nama Lengkap" icon="user" placeholder="Masukkan nama lengkap" autocomplete="name" :autofocus="true" />
<x-gi-field name="email" label="Email" icon="mail" type="email" placeholder="Masukkan email anda" autocomplete="email" />
<x-gi-field name="nomor_telepon" label="Nomor Telepon (opsional)" icon="phone" type="tel" placeholder="Contoh: 081234567890" autocomplete="tel" :required="false" />
<x-gi-field name="password" label="Password" icon="lock" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password" :toggle="true" />
<x-gi-field name="password_confirmation" label="Konfirmasi Password" icon="lock" type="password" placeholder="Ulangi password" autocomplete="new-password" :toggle="true" />

<button type="submit" class="mt-2 flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-forest text-sm font-medium text-white transition hover:bg-forestdark" data-test="register-user-button">
Daftar <x-gi name="arrow" class="h-4 w-4" />
</button>
</form>

<p class="mt-6 text-center text-sm text-slate2">
Sudah punya akun?
<a href="{{ route('login') }}" class="font-semibold text-forest hover:underline">Login</a>
</p>
@endsection