<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'GintanganCraft')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
theme: { extend: {
fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'], serif: ['Fraunces', 'Georgia', 'serif'] },
colors: { forest: '#3A5A41', forestdark: '#2F4934', cream: '#FAEDCC', ink: '#1F2937', slate2: '#495769' }
} }
}
</script>
</head>
<body class="bg-cream font-sans text-ink antialiased">
<div class="grid min-h-screen lg:grid-cols-2">

<aside class="relative hidden lg:block">
<img src="{{ asset('images/auth-bg.jpg') }}" alt="Produk anyaman bambu Gintangan" class="absolute inset-0 h-full w-full object-cover">
<div class="absolute inset-0 bg-gradient-to-t from-[#3A5A41]/95 via-[#3A5A41]/30 to-transparent"></div>
<div class="absolute inset-x-0 bottom-0 p-10 xl:p-14">
<h2 class="font-serif text-3xl font-semibold text-cream">@yield('caption_title', 'Dukung Pengrajin Lokal Gintangan')</h2>
<p class="mt-3 max-w-sm text-sm leading-relaxed text-cream/80">@yield('caption_text', 'Marketplace anyaman bambu Banyuwangi — handmade, ramah lingkungan, dan custom sesuai keinginanmu.')</p>
</div>
</aside>

<main class="flex items-center justify-center px-6 py-10">
<div class="w-full max-w-[380px]">
<a href="{{ route('pembeli.home') }}" class="mb-10 inline-flex items-center gap-2.5">
<span class="flex h-9 w-9 items-center justify-center rounded-xl bg-forest text-white"><x-gi name="leaf" class="h-4 w-4" /></span>
<span class="font-serif text-lg font-semibold">Gintangan<span class="text-forest">Craft</span></span>
</a>
@yield('form')
</div>
</main>

</div>
<script>
document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
btn.addEventListener('click', function () {
var input = document.getElementById(btn.getAttribute('data-toggle-password'));
input.type = input.type === 'password' ? 'text' : 'password';
});
});
</script>
</body>
</html>