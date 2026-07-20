@php
    $design = null;
    if (tenancy()->initialized) {
        $design = \App\Models\Tenant\DesignConfig::first();
    }
    $colors = $design->colors ?? [
        'primary-50' => '255 247 237',
        'primary-100' => '255 237 213',
        'primary-200' => '254 215 170',
        'primary-300' => '253 186 116',
        'primary-400' => '251 146 60',
        'primary-500' => '249 115 22',
        'primary-600' => '234 88 12',
        'primary-700' => '194 65 12',
        'primary-800' => '154 52 18',
        'primary-900' => '124 45 18',
        'surface-50' => '250 250 249',
        'surface-100' => '245 245 244',
        'surface-150' => '239 239 238',
        'surface-200' => '231 229 228',
        'surface-300' => '214 211 209',
        'surface-400' => '168 162 158',
        'surface-500' => '120 113 108',
        'surface-600' => '87 83 78',
        'surface-700' => '68 64 60',
        'surface-800' => '41 37 36',
        'surface-900' => '28 25 23',
        'success' => '22 163 74',
        'warning' => '217 119 6',
        'error' => '220 38 38',
        'info' => '37 99 235',
    ];
@endphp
<style>
    :root {
        --color-primary-50: {{ $colors['primary-50'] ?? '255 247 237' }};
        --color-primary-100: {{ $colors['primary-100'] ?? '255 237 213' }};
        --color-primary-200: {{ $colors['primary-200'] ?? '254 215 170' }};
        --color-primary-300: {{ $colors['primary-300'] ?? '253 186 116' }};
        --color-primary-400: {{ $colors['primary-400'] ?? '251 146 60' }};
        --color-primary-500: {{ $colors['primary-500'] ?? '249 115 22' }};
        --color-primary-600: {{ $colors['primary-600'] ?? '234 88 12' }};
        --color-primary-700: {{ $colors['primary-700'] ?? '194 65 12' }};
        --color-primary-800: {{ $colors['primary-800'] ?? '154 52 18' }};
        --color-primary-900: {{ $colors['primary-900'] ?? '124 45 18' }};
        --color-surface-50: {{ $colors['surface-50'] ?? '250 250 249' }};
        --color-surface-100: {{ $colors['surface-100'] ?? '245 245 244' }};
        --color-surface-150: {{ $colors['surface-150'] ?? '239 239 238' }};
        --color-surface-200: {{ $colors['surface-200'] ?? '231 229 228' }};
        --color-surface-300: {{ $colors['surface-300'] ?? '214 211 209' }};
        --color-surface-400: {{ $colors['surface-400'] ?? '168 162 158' }};
        --color-surface-500: {{ $colors['surface-500'] ?? '120 113 108' }};
        --color-surface-600: {{ $colors['surface-600'] ?? '87 83 78' }};
        --color-surface-700: {{ $colors['surface-700'] ?? '68 64 60' }};
        --color-surface-800: {{ $colors['surface-800'] ?? '41 37 36' }};
        --color-surface-900: {{ $colors['surface-900'] ?? '28 25 23' }};
        --color-success: {{ $colors['success'] ?? '22 163 74' }};
        --color-warning: {{ $colors['warning'] ?? '217 119 6' }};
        --color-error: {{ $colors['error'] ?? '220 38 38' }};
        --color-info: {{ $colors['info'] ?? '37 99 235' }};
        --color-surface-container-lowest: {{ $colors['surface-container-lowest'] ?? '255 255 255' }};
        --color-surface-container-low: {{ $colors['surface-container-low'] ?? '250 250 249' }};
        --color-surface-container: {{ $colors['surface-container'] ?? '245 245 244' }};
        --color-surface-container-high: {{ $colors['surface-container-high'] ?? '231 229 228' }};
        --color-surface-container-highest: {{ $colors['surface-container-highest'] ?? '214 211 209' }};
    }
</style>
