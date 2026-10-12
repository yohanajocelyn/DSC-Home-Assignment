@props([
    'message' => null,
])

@php
    $message = $message ?? session('success');
@endphp

@if ($message)
    <div {{ $attributes->merge(['class' => 'p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-2xs']) }}>
        <div class="flex items-center gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600 shrink-0" />
            <span class="font-semibold">{{ $message }}</span>
        </div>
        {{ $slot }}
    </div>
@endif
