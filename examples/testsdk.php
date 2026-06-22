<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Betnex\Betnex;
use Betnex\Utils\VerifyCallback;
use Betnex\Utils\CalculateBalance;
use Betnex\Utils\CallbackResponse;

$apiKey = getenv('BETNEX_API_KEY');

if (!$apiKey) {
    die("BETNEX_API_KEY environment variable not found\n");
}

$api = new Betnex(
    $apiKey,
    [
        "headerName" => "x-betnex-key",
        "debug" => true
    ]
);

try {

    echo PHP_EOL;
    echo "==============================" . PHP_EOL;
    echo "GET PROVIDERS" . PHP_EOL;
    echo "==============================" . PHP_EOL . PHP_EOL;

    $providers = $api->getProviders();

    print_r($providers);

    echo PHP_EOL;
    echo "==============================" . PHP_EOL;
    echo "GET GAMES" . PHP_EOL;
    echo "==============================" . PHP_EOL . PHP_EOL;

    $games = $api->getGames("SPRIBE");

    print_r($games);

    if (
        isset($games["games"]) &&
        count($games["games"]) > 0
    ) {

        echo PHP_EOL;
        echo "==============================" . PHP_EOL;
        echo "LAUNCH GAME" . PHP_EOL;
        echo "==============================" . PHP_EOL . PHP_EOL;

        $launch = $api->launchGame([
            "username" => "testuserking",
            "gameId" => $games["games"][0]["id"],
            "money" => 1000,
            "platform" => 1,
            "currency" => "INR",
            "home_url" => "https://google.com",
            "lang" => "en"
        ]);

        print_r($launch);
    }

    echo PHP_EOL;
    echo "==============================" . PHP_EOL;
    echo "CALLBACK VALIDATION EXAMPLE" . PHP_EOL;
    echo "==============================" . PHP_EOL . PHP_EOL;

    $callbackPayload = [
        "bet_amount" => 100,
        "win_amount" => 0,
        "member_account" => "testuser",
        "game_uid" => "a04d1f3eb8ccec8a4823bdf18e3f0e84",
        "game_round" => "9356359715438476370",
        "serial_number" => "955fbea9-6cd0-356c-8302-7326fa647c6d",
        "currency_code" => "INR",
        "api_key" => $apiKey,
        "game_name" => "AVIATOR",
        "game_provider" => "SPRIBE"
    ];

    $callback = VerifyCallback::verify(
        $callbackPayload,
        $apiKey
    );

    echo "Callback Verified:" . PHP_EOL;

    print_r($callback);

    $currentBalance = 1000;

    $newBalance = CalculateBalance::calculate(
        $currentBalance,
        $callback["bet_amount"],
        $callback["win_amount"]
    );

    echo PHP_EOL;
    echo "Calculated Balance: " . $newBalance . PHP_EOL;

    $response = CallbackResponse::create([
        "success" => true,
        "handle" => true,
        "money" => $newBalance,
        "msg" => "Callback processed successfully"
    ]);

    echo PHP_EOL;
    echo "Callback Response:" . PHP_EOL;

    print_r($response);

} catch (\Throwable $e) {

    echo PHP_EOL;
    echo "SDK Error:" . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;

    if (method_exists($e, "getStatus")) {
        echo "Status: " . $e->getStatus() . PHP_EOL;
    }

    if (method_exists($e, "getData")) {
        print_r($e->getData());
    }
}