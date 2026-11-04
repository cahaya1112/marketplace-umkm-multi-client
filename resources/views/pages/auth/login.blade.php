@extends('layouts.masuk')
@section('title', 'Login | GintanganCraft')

@section('form')
<h1 class="font-serif text-2xl font-semibold">Masuk ke Akun</h1>
<p class="mt-1 text-sm text-slate2">Selamat datang kembali di marketplace UMKM.</p>

@if (session('status'))
<div class="mt-5 rounded-lg bg-green-100 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-4">
@csrf
<x-gi-field name="email" label="Email" icon="mail" type="email" placeholder="Masukkan email anda" autocomplete="email" :autofocus="true" />
<x-gi-field name="password" label="Password" icon="lock" type="password" placeholder="Password" autocomplete="current-password" :toggle="true" />

<div class="flex items-center justify-between text-xs">
<label class="flex items-center gap-2 text-slate2">
<input type="checkbox" name="remember" class="h-3.5 w-3.5 rounded border-gray-300 text-forest focus:ring-forest">
Remember me
</label>
@if (Route::has('password.request'))
<a href="{{ route('password.request') }}" class="font-medium text-forest hover:underline">Lupa Password?</a>
@endif
</div>

<button type="submit" class="flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-forest text-sm font-medium text-white transition hover:bg-forestdark" data-test="login-button">
Login <x-gi name="arrow" class="h-4 w-4" />
</button>
</form>

<p class="mt-6 text-center text-sm text-slate2">
Belum punya akun?
<a href="{{ route('register') }}" class="font-semibold text-forest hover:underline">Registrasi Pembeli</a>
</p>
@endsection