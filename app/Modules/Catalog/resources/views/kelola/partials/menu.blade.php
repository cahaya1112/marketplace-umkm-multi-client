@if (session('sukses'))
    <div class="mb-4 rounded-xl bg-emerald-100 px-4 py-3 text-sm text-emerald-800">{{ session('sukses') }}</div>
@endif
@if (session('galat'))
    <div class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700">{{ session('galat') }}</div>
@endif