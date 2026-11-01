<?php

// Only the keys that differ from Livewire's defaults (vendor/livewire/livewire/config/livewire.php).
return [

    // Gallery videos are larger than Livewire's 12 MB default upload cap.
    // Keep this within PHP's upload_max_filesize / post_max_size.
    'temporary_file_upload' => [
        'disk' => null,
        'rules' => ['required', 'file', 'max:40960'], // KB → 40 MB
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 10,
        'cleanup' => true,
    ],

];
