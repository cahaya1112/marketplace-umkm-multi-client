<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Marketplace UMKM')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
theme: { extend: {
fontFamily: { sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
colors: { forest: '#3E5D3C', forestdark: '#2F4A2E', cream: '#F9ECCD', sand: '#E3D9BF', tan: '#D2A874', ink: '#232934' }
} }
}
</script>
</head>
<body class="bg-cream font-sans text-ink antialiased">

<header class="sticky top-0 z-50 bg-white shadow-sm">
<div class="mx-auto flex h-14 max-w-6xl items-center justify-between px-4">
<a href="{{ route('pembeli.home') }}" class="flex items-center gap-2">
<span class="flex h-8 w-8 items-center justify-center rounded-lg bg-forest text-white"><x-gi name="bag" class="h-4 w-4" /></span>
<span class="text-sm font-semibold">Marketplace UMKM</span>
</a>

<nav class="hidden items-center gap-6 text-xs md:flex">
<a href="/katalog" class="flex items-center gap-1.5 hover:text-forest"><x-gi name="box" class="h-3.5 w-3.5" />Katalog</a>
<a href="#umkm" class="flex items-center gap-1.5 hover:text-forest"><x-gi name="store" class="h-3.5 w-3.5" />UMKM</a>
<a href="#kategori" class="flex items-center gap-1.5 hover:text-forest"><x-gi name="tag" class="h-3.5 w-3.5" />Kategori</a>
<a href="#bantuan" class="flex items-center gap-1.5 hover:text-forest"><x-gi name="help" class="h-3.5 w-3.5" />Bantuan</a>
</nav>

<div class="flex items-center gap-3 text-xs">
@auth
<span class="hidden sm:inline">Halo, {{ \Illuminate\Support\Str::words(auth()->user()->nama, 1, '') }}</span>
<a href="{{ route('dashboard') }}" class="hover:text-forest">Dashboard</a>
<form method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit" class="rounded-lg bg-forest px-4 py-2 font-medium text-white hover:bg-forestdark">Keluar</button>
</form>
@else
<a href="{{ route('login') }}" class="flex items-center gap-1.5 hover:text-forest"><x-gi name="login" class="h-3.5 w-3.5" />Login</a>
<a href="{{ route('register') }}" class="rounded-lg bg-forest px-4 py-2 font-medium text-white hover:bg-forestdark">Registrasi</a>
@endauth
</div>
</div>
</header>

@if (session('status'))
<div class="bg-tan/30 px-4 py-2 text-center text-xs">{{ session('status') }}</div>
@endif

<main>@yield('content')</main>

<footer id="bantuan" class="bg-forest text-white/80">
<div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 text-xs md:grid-cols-4">
<div>
<div class="mb-3 flex items-center gap-2 text-white">
<span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15"><x-gi name="bag" class="h-4 w-4" /></span>
<span class="text-sm font-semibold">Marketplace UMKM</span>
</div>
<p class="leading-relaxed">Platform marketplace produk kerajinan lokal Indonesia. Mendukung UMKM dengan teknologi multi-client.</p>
</div>
<div>
<h4 class="mb-3 text-sm font-semibold text-white">Menu</h4>
<ul class="space-y-2"><li>Katalog</li><li>UMKM</li><li>Kategori</li><li>Bantuan</li></ul>
</div>
<div>
<h4 class="mb-3 text-sm font-semibold text-white">Kategori</h4>
<ul class="space-y-2">
@forelse (($kategori ?? []) as $k)
<li>{{ $k->nama }}</li>
@empty
<li>-</li>
@endforelse
</ul>
</div>
<div>
<h4 class="mb-3 text-sm font-semibold text-white">Kontak</h4>
<ul class="space-y-2"><li>Email: info@marketplace-umkm.id</li><li>Telepon: 0800-1234-5678</li><li>Senin - Jumat, 08:00 - 17:00</li></ul>
</div>
</div>
<div class="border-t border-white/10 py-5 text-center text-[11px] text-white/60">&copy; {{ date('Y') }} Marketplace UMKM. Semua hak dilindungi.</div>
</footer>
</body>
</html>