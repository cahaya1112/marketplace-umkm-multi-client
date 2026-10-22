<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard UMKM' }}</title>
</head>
<body style="font-family: sans-serif; margin: 0; display: flex; min-height: 100vh;">
    <aside style="width: 250px; background: white; border-right: 1px solid #e5e7eb; padding: 1.5rem;">
        <h3 style="color: #2563eb; margin-top: 0;">Toko UMKM</h3>
        <nav style="display: flex; flex-direction: column; gap: 0.5rem;">
            <a href="#" style="text-decoration: none; color: #4b5563;">Beranda</a>
            <a href="#" style="text-decoration: none; color: #4b5563;">Kelola Produk</a>
            <a href="#" style="text-decoration: none; color: #4b5563;">Pesanan Masuk</a>
        </nav>
    </aside>
    <div style="flex: 1; display: flex; flex-direction: column; background: #f9fafb;">
        <header style="background: white; padding: 1rem 2rem; border-bottom: 1px solid #e5e7eb;">
            <strong>{{ $title ?? 'Dashboard' }}</strong>
        </header>
        <main style="padding: 2rem;">
            {{ $slot }}
        </main>
    </div>
</body>
</html>