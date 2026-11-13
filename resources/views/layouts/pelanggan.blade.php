
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
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        forest: '#3E5D3C',
                        forestdark: '#2F4A2E',
                        cream: '#F9ECCD',
                        sand: '#E3D9BF',
                        tan: '#D2A874',
                        ink: '#232934'
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen bg-cream font-sans text-ink antialiased">

@php
    $nav = [
        [
            'label' => 'Home',
            'icon' => 'home',
            'href' => route('pembeli.home'),
            'active' => request()->routeIs('pembeli.home'),
        ],
        [
            'label' => 'Katalog',
            'icon' => 'box',
            'href' => route('katalog.index'),
            'active' => request()->routeIs('katalog.*', 'produk.*'),
        ],
        [
            'label' => 'UMKM',
            'icon' => 'store',
            'href' => url('/#umkm'),
            'active' => false,
        ],
        [
            'label' => 'Kategori',
            'icon' => 'tag',
            'href' => url('/#kategori'),
            'active' => false,
        ],
        [
            'label' => 'Keranjang',
            'icon' => 'cart',
            'href' => route('keranjang.index'),
            'active' => request()->routeIs('keranjang.*'),
        ],
        [
            'label' => 'Custom Saya',
            'icon' => 'box',
            'href' => route('custom.saya'),
            'active' => request()->routeIs('custom.*'),
        ],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-gray-100 bg-white shadow-sm">
    <div class="mx-auto flex min-h-16 max-w-6xl flex-wrap items-center justify-between gap-x-5 gap-y-2 px-4 py-2 lg:flex-nowrap">

        {{-- Brand --}}
        <a href="{{ route('pembeli.home') }}"
           class="flex shrink-0 items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-forest text-sm font-bold text-white">
                M
            </span>

            <span class="whitespace-nowrap text-sm font-semibold text-ink">
                Marketplace UMKM
            </span>
        </a>

        {{-- Navigasi utama --}}
        <nav class="order-3 flex w-full items-center gap-1 overflow-x-auto pb-1 text-[13px] lg:order-none lg:w-auto lg:flex-1 lg:justify-center lg:pb-0">

            @foreach ($nav as $menu)
                <a href="{{ $menu['href'] }}"
                   class="flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-lg px-3 py-2 transition
                   {{ $menu['active']
                       ? 'bg-forest font-medium text-white'
                       : 'text-ink/80 hover:bg-sand/50 hover:text-forest' }}">

                    <x-gi :name="$menu['icon']" class="h-4 w-4" />

                    <span>{{ $menu['label'] }}</span>

                    @if ($menu['label'] === 'Keranjang' && ($jumlahKeranjang ?? 0) > 0)
                        <span class="flex min-w-5 items-center justify-center rounded-full bg-tan/40 px-1.5 py-0.5 text-[10px] font-semibold">
                            {{ $jumlahKeranjang }}
                        </span>
                    @endif
                </a>
            @endforeach

        </nav>

        {{-- Aksi akun --}}
        <div class="flex shrink-0 items-center gap-3">
            <span class="hidden text-xs text-gray-500 sm:block">
                Halo, {{ \Illuminate\Support\Str::words(auth()->user()->nama ?? 'Pelanggan', 1, '') }}
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                        class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium text-[#E8694A] transition hover:bg-red-50">
                    <x-gi name="logout" class="h-4 w-4" />
                    Keluar
                </button>
            </form>
        </div>

    </div>
</header>

{{-- Notifikasi status --}}
@if (session('status'))
    <div class="border-b border-[#E3D9BF] bg-[#F3E4C3] px-4 py-3 text-center text-sm text-ink">
        {{ session('status') }}
    </div>
@endif

{{-- Konten halaman --}}
<main class="mx-auto w-full max-w-6xl px-4 py-8">
    @yield('content')
</main>

</body>
</html>
