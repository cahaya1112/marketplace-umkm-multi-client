@props(['name', 'label', 'icon', 'type' => 'text', 'placeholder' => '', 'autocomplete' => null, 'toggle' => false, 'autofocus' => false, 'required' => true])
<div>
<label for="{{ $name }}" class="mb-1.5 block text-sm font-medium">{{ $label }}</label>
<div class="relative">
<span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate2"><x-gi :name="$icon" class="h-5 w-5" /></span>
<input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" @if ($type !== 'password') value="{{ old($name) }}" @endif @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif @if ($autofocus) autofocus @endif @if ($required) required @endif placeholder="{{ $placeholder }}"
class="h-11 w-full rounded-xl border bg-white pl-11 {{ $toggle ? 'pr-11' : 'pr-4' }} text-sm shadow-md shadow-black/5 outline-none placeholder:text-slate2/70 focus:ring-2 focus:ring-forest/40 {{ $errors->has($name) ? 'border-red-400' : 'border-transparent' }}">
@if ($toggle)
<button type="button" data-toggle-password="{{ $name }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate2 hover:text-ink" aria-label="Lihat password"><x-gi name="eye" class="h-5 w-5" /></button>
@endif
</div>
@error($name)<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
</div>