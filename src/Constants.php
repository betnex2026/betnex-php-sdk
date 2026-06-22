<?php

namespace Betnex;

class Constants
{
    public const DEFAULT_CONFIG = [
        'baseUrl' => 'http://livecasinoapi.betnex.co:8011',
        'timeout' => 10000,
        'retries' => 3,
        'headerName' => 'x-betnex-key',
        'supportedHeaders' => [
            'x-betnex-key',
            'x-turnkeyxgaming-key'
        ]
    ];

    public const ENDPOINTS = [
        'PROVIDERS' => '/casino/getallproviders',
        'GAMES' => '/casino/getallgamesandprovider',
        'GAME_URL' => '/casino/getgameurl'
    ];

    public const VERSION = '1.0.3';
}