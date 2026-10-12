@props([
    'title' => 'Action could not be processed:',
    'message' => null,
    'bag' => null,
])

@php
    $errorBag = $bag ?? (isset($errors) && $errors instanceof \Illuminate\Support\ViewErrorBag ? $errors : session('errors'));
    $hasBagErrors = $errorBag && method_exists($errorBag, 'any') && $errorBag->any();
    $sessionError = $message ?? session('error');
@endphp

@if ($hasBagErrors || $sessionError)
    <div {{ $attributes->merge(['class' => 'p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-2xs']) }}>
        <div class="flex items-start gap-3">
            <x-heroicon-o-exclamation-circle class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
            <div>
                @if ($hasBagErrors)
                    <p class="font-bold mb-1">{{ $title }}</p>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errorBag->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @elseif ($sessionError)
                    <p class="font-bold">{{ $sessionError }}</p>
                @endif
            </div>
        </div>
        {{ $slot }}
    </div>
@endif
