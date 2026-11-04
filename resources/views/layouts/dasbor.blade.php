<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Dashboard')</title>
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

<aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-[#F8F3E7] px-3 py-5 md:flex">
<div class="mb-6 flex items-center gap-3 px-2">
<span class="flex h-10 w-10 items-center justify-center rounded-xl bg-forest text-base font-bold text-white">M</span>
<div class="min-w-0">
<div class="text-[13px] font-semibold leading-tight">Marketplace UMKM</div>
<div class="truncate text-[11px] text-gray-500">{{ $brandSub }}</div>
</div>
</div>

<nav class="flex-1 overflow-y-auto">
@foreach ($menu as $m)
<a href="{{ $m['href'] }}" class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] {{ ! empty($m['active']) ? 'bg-forest text-cream shadow' : 'text-ink/80 hover:bg-white/60' }}">
<x-gi :name="$m['icon']" class="h-4 w-4" />
<span class="flex-1">{{ $m['label'] }}</span>
@if (! empty($m['badge']))
<span class="rounded-full bg-[#E8694A] px-1.5 text-[10px] font-semibold text-white">{{ $m['badge'] }}</span>
@endif
</a>
@endforeach
</nav>

<form method="POST" action="{{ route('logout') }}" class="border-t border-black/10 pt-3">
@csrf
<button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] text-[#E8694A] hover:bg-white/60"><x-gi name="logout" class="h-4 w-4" />Keluar</button>
</form>
</aside>

<header class="flex items-center justify-between bg-[#F8F3E7] px-4 py-3 md:hidden">
<span class="text-sm font-semibold">Marketplace UMKM</span>
<form method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit" class="text-xs font-medium text-[#E8694A]">Keluar</button>
</form>
</header>

<div class="md:pl-64">
<main class="px-5 py-8 lg:px-8">
@yield('content')
</main>
</div>
</body>
</html>