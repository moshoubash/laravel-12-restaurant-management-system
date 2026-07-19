<?php

return [
    'class_namespace' => 'App\\Livewire',
    'view_path' => resource_path('views/livewire'),
    'layout' => 'layouts.app',
    'asset_url' => null,
    'app_url' => null,
    'middleware_group' => 'web',
    'temporary_file_upload' => [
        'disk' => null,
        'rules' => null,
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_size' => 12288,
        'cleanup' => true,
    ],
    'manifest_path' => null,
    'back_button_cache' => false,
    'render_on_redirect' => false,
    'inject_assets' => true,
    'inject_morph_markers' => true,
    'pagination_theme' => 'tailwind',
];
