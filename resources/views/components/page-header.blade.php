@props(['title', 'subtitle' => null, 'illustration' => null])

<div class="flex items-center justify-between gap-6 mb-8">
    <div class="min-w-0">
        <h1 class="page-title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
        {{ $slot ?? '' }}
    </div>

    @if ($illustration)
        <div class="hidden sm:block shrink-0">
            @php
                $basename = pathinfo($illustration, PATHINFO_FILENAME);
                $ext = pathinfo($illustration, PATHINFO_EXTENSION);
                $lightSrc = 'images/illustrations/'.$basename.'_light.'.$ext;
                $darkSrc = 'images/illustrations/'.$basename.'_dark.'.$ext;
                $hasLightDark = file_exists(public_path($lightSrc)) && file_exists(public_path($darkSrc));
            @endphp
            @if ($hasLightDark)
                <img src="{{ asset($lightSrc) }}" alt=""
                     class="h-28 md:h-40 lg:h-52 w-auto object-contain select-none pointer-events-none dark:hidden block">
                <img src="{{ asset($darkSrc) }}" alt=""
                     class="h-28 md:h-40 lg:h-52 w-auto object-contain select-none pointer-events-none hidden dark:block">
            @else
                <img src="{{ asset('images/illustrations/'.$illustration) }}" alt=""
                     class="h-28 md:h-40 lg:h-52 w-auto object-contain select-none pointer-events-none">
            @endif
        </div>
    @endif
</div>
