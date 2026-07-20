<span class="badge badge-{{ $status ?? 'default' }} {{ $class ?? '' }}" {{ $attributes }}>
    @if($dot ?? true)
        <span class="badge-dot badge-dot-{{ $status ?? 'default' }}" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
