@props(['active' => false, 'icon' => null])

@php
$classes = $active
            ? 'flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-start text-base font-semibold text-brand-600 bg-brand-50 dark:bg-brand-900/30 dark:text-brand-300 transition duration-150 ease-in-out'
            : 'flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-start text-base font-medium text-ink-500 hover:text-ink-800 hover:bg-ink-50 dark:text-ink-400 dark:hover:text-white dark:hover:bg-ink-800 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <span class="icon icon-md">{{ $icon }}</span>
    @endif
    {{ $slot }}
</a>
