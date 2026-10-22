<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Marketplace UMKM' }}</title>
</head>
<body style="font-family: sans-serif; margin: 0; background: #f9fafb;">
    <header style="background: white; padding: 1rem 2rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between;">
        <strong>Marketplace UMKM</strong>
        <div>
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('login') }}">Masuk</a>
                <a href="{{ route('register') }}">Daftar</a>
            @endauth
        </div>
    </header>
    <main style="padding: 2rem;">
        {{ $slot }}
    </main>
</body>
</html>