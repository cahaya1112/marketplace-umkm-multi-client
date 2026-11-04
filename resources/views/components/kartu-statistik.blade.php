@props(['icon', 'value', 'label', 'warna' => 'bg-[#E4ECE4] text-forest'])
<div class="flex items-center gap-3 rounded-xl border border-[#E8DFC6] bg-white p-4 shadow-sm">
<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $warna }}"><x-gi :name="$icon" class="h-5 w-5" /></span>
<div>
<div class="text-xl font-semibold leading-tight">{{ number_format($value) }}</div>
<div class="text-[11px] text-gray-500">{{ $label }}</div>
</div>
</div>