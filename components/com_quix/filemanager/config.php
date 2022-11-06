<?php

return [
    "plugins" => [
		\FileManager\Plugins\General::class
	],
	'root' => QUIXNXT_JMEDIA_PATH,
    'upload' => [
        'allowed_types' => [
            // Images
            'image/jpeg',  
            'image/png', 
            'image/svg',
            'image/svg+xml',
            'image/gif',
            'image/x-icon',
            'image/bmp',
            // Document
            'text/css',
            'text/csv',
            'text/html',
            'text/plain',
            'application/xml',
            'application/vnd.ms-excel',
            // Application
            'application/pdf',
            'application/x-javascript',
            'application/msword',
            'application/json',
            'application/excel',
            'application/powerpoint',
            // Audio + Video
            'application/ogg',
            'video/quicktime',
            'video/mp4',
            'audio/mpeg',
            'audio/x-wav',
            // Archive
            'application/zip',
            'application/x-zip'
        ]
    ]
];
