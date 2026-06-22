# Betnex PHP SDK

Official PHP SDK for the Betnex Casino API.

Integrate 100+ providers and 10,000+ casino games including Slots, Live Casino, Crash Games, Sportsbook, Virtual Sports and more.

---

## Requirements

- PHP 8.1+
- Composer

---

## Installation

Install via Composer:

```bash
composer require betnex/sdk
```

---

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use Betnex\Betnex;

$api = new Betnex(
    'YOUR_API_KEY',
    [
        'headerName' => 'x-betnex-key'
    ]
);

$providers = $api->getProviders();

print_r($providers);
```

---

## Configuration

```php
$api = new Betnex(
    'YOUR_API_KEY',
    [
        'headerName' => 'x-betnex-key',
        'timeout' => 10000,
        'retries' => 3,
        'debug' => true
    ]
);
```

### Supported Authentication Headers

```text
x-betnex-key
x-turnkeyxgaming-key
```

---

# Get Providers

Retrieve all available providers.

```php
$providers = $api->getProviders();

print_r($providers);
```

### Example Response

```json
{
  "success": true,
  "providers": ["SPRIBE", "PRAGMATICSLOTS", "EVOLUTIONLIVE"]
}
```

---

# Get Games

Retrieve all games for a provider.

```php
$games = $api->getGames('SPRIBE');

print_r($games);
```

### Example Response

```json
{
  "success": true,
  "provider": "SPRIBE",
  "totalGames": 16,
  "games": []
}
```

---

# Launch Game

Generate a secure game launch URL.

```php
$launch = $api->launchGame([
    'username' => 'testuser',
    'gameId' => 'a04d1f3eb8ccec8a4823bdf18e3f0e84',
    'money' => 1000,
    'platform' => 1,
    'currency' => 'INR',
    'home_url' => 'https://example.com',
    'lang' => 'en'
]);

print_r($launch);
```

### Example Response

```json
{
  "code": 0,
  "msg": "Success",
  "payload": {
    "game_launch_url": "https://livecasinoapi.betnex.co/game?token=...",
    "game_name": "Aviator",
    "provider": "SPRIBE",
    "expires_in": 60
  }
}
```

---

# Callback Validation

Validate callback payloads received from Betnex.

```php
use Betnex\Utils\VerifyCallback;

$callback = VerifyCallback::verify(
    $payload,
    $apiKey
);

print_r($callback);
```

### Example Callback Payload

```php
$payload = [
    'bet_amount' => 100,
    'win_amount' => 0,
    'member_account' => 'testuser',
    'game_uid' => 'a04d1f3eb8ccec8a4823bdf18e3f0e84',
    'game_round' => '9356359715438476370',
    'serial_number' => '955fbea9-6cd0-356c-8302-7326fa647c6d',
    'currency_code' => 'INR',
    'api_key' => 'YOUR_API_KEY',
    'game_name' => 'AVIATOR',
    'game_provider' => 'SPRIBE'
];
```

### Example Response

```php
Array
(
    [valid] => true
    [bet_amount] => 100
    [win_amount] => 0
    [member_account] => testuser
    ...
)
```

---

# Calculate Balance

Helper function for balance calculations.

Formula:

```text
currentBalance - betAmount + winAmount
```

Example:

```php
use Betnex\Utils\CalculateBalance;

$newBalance = CalculateBalance::calculate(
    1000,
    100,
    50
);

echo $newBalance;
```

Output:

```text
950
```

---

# Callback Response Helper

Generate standardized callback responses.

```php
use Betnex\Utils\CallbackResponse;

$response = CallbackResponse::create([
    'success' => true,
    'handle' => true,
    'money' => 900,
    'msg' => 'Callback processed successfully'
]);

print_r($response);
```

### Example Response

```json
{
  "success": true,
  "msg": "Callback processed successfully",
  "handle": true,
  "money": 900
}
```

---

# Error Handling

```php
try {

    $providers = $api->getProviders();

} catch (\Throwable $e) {

    echo $e->getMessage();
}
```

---

# Debug Mode

Enable SDK request logging.

```php
$api = new Betnex(
    $apiKey,
    [
        'debug' => true
    ]
);
```

---

# API Endpoints

```http
GET  /casino/getallproviders
GET  /casino/getallgamesandprovider
POST /casino/getgameurl
```
---

# Version

```text
SDK Version: 1.0.3
```

---

# Support

### Website

https://casinoapi.betnex.co

### Email

contact@betnex.co

---

# License

MIT License

Copyright (c) Betnex
