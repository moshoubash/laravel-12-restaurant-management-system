@php
    $design = null;
    if (tenancy()->initialized) {
        $design = \App\Models\Tenant\DesignConfig::first();
    }
    $colors = $design->colors ?? [
        'primary' => '232 89 12',
        'primary-container' => '255 237 213',
        'on-surface' => '30 30 30',
        'surface-container-lowest' => '255 255 255',
        'surface-container-low' => '250 250 249',
        'surface-container' => '245 245 244',
        'surface-container-high' => '231 229 228',
        'surface-container-highest' => '214 211 209',
        'secondary' => '120 113 108',
        'error' => '220 38 38',
        'on-primary-container' => '87 33 0',
    ];
@endphp
<style>
    :root {
        --color-primary-rgb: {{ $colors['primary'] ?? '232 89 12' }};
        --color-primary-container-rgb: {{ $colors['primary-container'] ?? '255 237 213' }};
        --color-on-surface-rgb: {{ $colors['on-surface'] ?? '30 30 30' }};
        --color-surface-container-lowest-rgb: {{ $colors['surface-container-lowest'] ?? '255 255 255' }};
        --color-surface-container-low-rgb: {{ $colors['surface-container-low'] ?? '250 250 249' }};
        --color-surface-container-rgb: {{ $colors['surface-container'] ?? '245 245 244' }};
        --color-surface-container-high-rgb: {{ $colors['surface-container-high'] ?? '231 229 228' }};
        --color-surface-container-highest-rgb: {{ $colors['surface-container-highest'] ?? '214 211 209' }};
        --color-secondary-rgb: {{ $colors['secondary'] ?? '120 113 108' }};
        --color-error-rgb: {{ $colors['error'] ?? '220 38 38' }};
        --color-on-primary-container-rgb: {{ $colors['on-primary-container'] ?? '87 33 0' }};
    }
</style>
