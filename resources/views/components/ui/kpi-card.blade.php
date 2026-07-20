<div class="kpi-card @if($accent) card-accent-{{ $accent }} @endif">
    <div class="kpi-header">
        <span class="kpi-label">{{ $label }}</span>
        @if($icon ?? false)
            <div class="kpi-icon">
                {!! $icon !!}
            </div>
        @endif
    </div>
    <p class="kpi-value">{{ $value }}</p>
    @if($trend ?? false)
        <div class="kpi-trend kpi-trend-{{ $direction ?? 'up' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ ($direction ?? 'up') === 'up' ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"/>
            </svg>
            <span>{{ $trend }}</span>
        </div>
    @endif
</div>
