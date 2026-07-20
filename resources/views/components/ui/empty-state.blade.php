<div class="empty-state">
    @if($icon ?? false)
        <div class="empty-state-icon text-surface-300">
            {!! $icon !!}
        </div>
    @else
        <svg class="w-16 h-16 text-surface-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
    @endif
    <h3 class="empty-state-title">{{ $title }}</h3>
    <p class="empty-state-description">{{ $description ?? '' }}</p>
    @if($action ?? false)
        <button class="btn-primary mt-4" {{ $actionAttributes ?? '' }}>{{ $action }}</button>
    @endif
</div>
