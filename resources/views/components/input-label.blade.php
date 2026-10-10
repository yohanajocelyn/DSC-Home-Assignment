@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-primary-800 mb-2']) }}>
    {{ $value ?? $slot }}
</label>
