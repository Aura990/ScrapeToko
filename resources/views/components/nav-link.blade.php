@props(['active' => false, 'icon' => null])

@php
$classes = $active
            ? 'inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold leading-5 bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300 transition duration-150 ease-in-out'
            : 'inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium leading-5 text-ink-400 hover:text-ink-700 hover:bg-ink-50 dark:text-ink-400 dark:hover:text-ink-100 dark:hover:bg-ink-800 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <span class="icon icon-sm">{{ $icon }}</span>
    @endif
    {{ $slot }}
</a>
