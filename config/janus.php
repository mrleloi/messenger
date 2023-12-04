<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Janus Client Configurations
    |--------------------------------------------------------------------------
    |
    */
    'server_endpoint' => env('JANUS_SERVER_ENDPOINT'),
    'admin_server_endpoint' => env('JANUS_ADMIN_SERVER_ENDPOINT'),
    'verify_ssl' => env('JANUS_VERIFY_SSL', true),
    'debug' => env('JANUS_DEBUG', false),
    'admin_secret' => env('JANUS_ADMIN_SECRET'),
    'api_secret' => env('JANUS_API_SECRET'),
    'video_room_secret' => env('JANUS_VIDEO_ROOM_SECRET'), // for set admin_key in videoRoom

    //custom
    'client_debug' => env('JANUS_CLIENT_DEBUG', false),
    'main_servers' => [
//        "wss://windysoft.asia",
//        "https://windysoft.asia/janus",
        "wss://127.0.0.1/janus-ws",
        "https://127.0.0.1/janus/janus",
    ],
    'ice_servers' => [
        [
            'urls' => 'stun:stun1.l.google.com:19302'
        ]
    ],
];
