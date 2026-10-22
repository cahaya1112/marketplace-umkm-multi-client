<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Console' }}</title>
</head>
<body style="font-family: sans-serif; margin: 0; display: flex; min-height: 100vh;">
    <aside style="width: 250px; background: #1e293b; color: white; padding: 1.5rem;">
        <h3 style="margin-top: 0;">Admin Platform</h3>
        <nav style="display: flex; flex-direction: column; gap: 0.5rem;">
            <a href="#" style="text-decoration: none; color: #cbd5e1;">Overview</a>
            <a href="#" style="text-decoration: none; color: #cbd5e1;">Verifikasi UMKM</a>
            <a href="#" style="text-decoration: none; color: #cbd5e1;">Kategori Global</a>
        </nav>
    </aside>
    <div style="flex: 1; display: flex; flex-direction: column; background: #f8fafc;">
        <header style="background: white; padding: 1rem 2rem; border-bottom: 1px solid #e2e8f0;">
            <strong>{{ $title ?? 'Dashboard Admin' }}</strong>
        </header>
        <main style="padding: 2rem;">
            {{ $slot }}
        </main>
    </div>
</body>
</html>