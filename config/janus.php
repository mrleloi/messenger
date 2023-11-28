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
        "wss://janus.windysoft.asia/janus-ws",
        "https://janus.windysoft.asia/janus/janus",
    ],
    'ice_servers' => [
        [
            'urls' => 'stun:stun1.l.google.com:19302'
        ]
    ],
];
