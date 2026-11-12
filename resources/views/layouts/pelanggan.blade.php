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
@php
$nav = [
   ['Home', 'home', route('pembeli.home'), request()->routeIs('pembeli.home')],
['Katalog', 'box', route('katalog.index'), request()->routeIs('katalog.*', 'produk.*')],
['UMKM', 'store', '#umkm', false],
['Kategori', 'tag', '#kategori', false],
['Keranjang', 'cart', route('keranjang.index'), false],
['Custom Saya', 'box', route('custom.saya'), false],
['Profil', 'user', '#', false],
];
@endphp

<header class="bg-white shadow-sm">
<div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-x-6 gap-y-1 px-4 py-2">
<a href="{{ route('pembeli.home') }}" class="flex items-center gap-2">
<span class="flex h-8 w-8 items-center justify-center rounded-lg bg-forest text-sm font-bold text-white">M</span>
<span class="text-sm font-semibold">Marketplace UMKM</span>
</a>

<nav class="order-last flex w-full items-center gap-1 overflow-x-auto text-xs lg:order-none lg:w-auto lg:flex-1">
@foreach ($nav as [$label, $icon, $href, $aktif])
<a href="{{ $href }}" class="flex items-center gap-1.5 whitespace-nowrap rounded-lg px-3 py-1.5 {{ $aktif ? 'bg-forest text-white' : 'hover:bg-sand/50' }}">
<x-gi :name="$icon" class="h-3.5 w-3.5" />{{ $label }}
@if ($label === 'Keranjang' && ($jumlahKeranjang ?? 0) > 0)
<span class="rounded-full bg-tan/40 px-1.5 text-[10px]">{{ $jumlahKeranjang }}</span>
@endif
</a>
@endforeach
</nav>

<div class="flex items-center gap-4 text-xs">
<a href="#" class="relative text-ink/80 hover:text-forest" aria-label="Notifikasi">
<x-gi name="bell" class="h-4 w-4" />
@if (($jumlahNotifikasi ?? 0) > 0)
<span class="absolute -right-2 -top-2 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#E8694A] px-1 text-[9px] font-semibold text-white">{{ $jumlahNotifikasi }}</span>
@endif
</a>
<form method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit" class="flex items-center gap-1.5 font-medium text-[#E8694A] hover:underline"><x-gi name="logout" class="h-3.5 w-3.5" />Keluar</button>
</form>
</div>
</div>
</header>

@if (session('status'))
<div class="bg-tan/30 px-4 py-2 text-center text-xs">{{ session('status') }}</div>
@endif

<main class="mx-auto max-w-6xl px-4 py-6">
@yield('content')
</main>
</body>
</html>