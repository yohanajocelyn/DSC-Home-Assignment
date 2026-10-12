<x-flash-error {{ $attributes }} :title="$title ?? 'Action could not be processed:'" :message="$message ?? null" :bag="$bag ?? null">
    {{ $slot }}
</x-flash-error>
