@props(['status'])
@php
$peta = [
'MENUNGGU_PEMBAYARAN' => ['Menunggu Pembayaran', 'bg-amber-50 text-amber-600 border-amber-200'],
'DIPROSES' => ['Diproses', 'bg-sky-50 text-sky-700 border-sky-200'],
'DIKIRIM' => ['Dikirim', 'bg-indigo-50 text-indigo-700 border-indigo-200'],
'SELESAI' => ['Selesai', 'bg-green-100 text-green-800 border-green-200'],
'DIBATALKAN' => ['Dibatalkan', 'bg-red-50 text-red-600 border-red-200'],
'PENDING' => ['Menunggu', 'bg-amber-50 text-amber-600 border-amber-200'],
'DIAJUKAN' => ['Diajukan', 'bg-amber-50 text-amber-600 border-amber-200'],
'DISETUJUI' => ['Disetujui', 'bg-green-100 text-green-800 border-green-200'],
'DITOLAK' => ['Ditolak', 'bg-red-50 text-red-600 border-red-200'],
'AKTIF' => ['Aktif', 'bg-green-100 text-green-800 border-green-200'],
'NONAKTIF' => ['Nonaktif', 'bg-gray-100 text-gray-600 border-gray-200'],
];
$kunci = strtoupper((string) $status);
$teks = $peta[$kunci][0] ?? ucfirst(strtolower(str_replace('_', ' ', (string) $status)));
$kelas = $peta[$kunci][1] ?? 'bg-gray-100 text-gray-600 border-gray-200';
@endphp
<span class="whitespace-nowrap rounded-full border px-2.5 py-0.5 text-[11px] font-medium {{ $kelas }}">{{ $teks }}</span>