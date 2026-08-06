<?php

// config/debugbar.php
return [
    'enabled' => env('DEBUGBAR_ENABLED', true),
    
    'capture_ajax' => false, 
    
    'except' => [
        'notifications/*', 
        'api/*',
    ],
    
    'storage' => [
        'enabled' => true,
        'driver'  => 'file',
        'path'    => storage_path('debugbar'),
    ],
];