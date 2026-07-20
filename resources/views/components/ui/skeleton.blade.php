<div class="animate-pulse space-y-{{ $space ?? 3 }}">
    @for($i = 0; $i < ($rows ?? 3); $i++)
        <div class="skeleton h-{{ $height ?? 4 }} w-{{ $widths[$i] ?? 'full' }} rounded-{{ $rounded ?? 'lg' }}"></div>
    @endfor
    {{ $slot ?? '' }}
</div>
