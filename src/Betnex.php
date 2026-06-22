<?php

namespace Betnex;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Betnex\Exceptions\BetnexException;

class Betnex
{
    private string $apiKey;

    private array $config;

    private Client $client;

    private const SUPPORTED_HEADERS = [
        'x-betnex-key',
        'x-turnkeyxgaming-key'
    ];

    public function __construct(
        string $apiKey,
        array $options = []
    ) {
        if (empty($apiKey)) {
            throw new BetnexException(
                "A valid API key is required"
            );
        }

        $this->apiKey = $apiKey;

        $this->config = array_merge(
            Constants::DEFAULT_CONFIG,
            [
                'debug' => false
            ],
            $options
        );

        $this->validateConfig();

        $this->client = new Client([
            'base_uri' => $this->config['baseUrl'],
            'timeout' => $this->config['timeout'] / 1000,
            'headers' => [
                $this->config['headerName']
                    => $this->apiKey,

                'Content-Type'
                    => 'application/json',

                'Accept'
                    => 'application/json'
            ]
        ]);
    }

    private function validateConfig(): void
    {
        if (
            !in_array(
                $this->config['headerName'],
                self::SUPPORTED_HEADERS
            )
        ) {
            throw new BetnexException(
                'Unsupported header name. Supported values: '
                . implode(', ', self::SUPPORTED_HEADERS)
            );
        }

        if (
            empty($this->config['baseUrl']) ||
            !is_string($this->config['baseUrl'])
        ) {
            throw new BetnexException(
                'A valid baseUrl is required'
            );
        }

        if ((int)$this->config['timeout'] <= 0) {
            throw new BetnexException(
                'timeout must be greater than 0'
            );
        }

        if ((int)$this->config['retries'] < 0) {
            throw new BetnexException(
                'retries cannot be negative'
            );
        }
    }

    private function sleep(int $milliseconds): void
    {
        usleep($milliseconds * 1000);
    }

    private function isValidUrl(string $url): bool
    {
        return filter_var(
            $url,
            FILTER_VALIDATE_URL
        ) !== false;
    }

    private function validateRequired(
        array $payload,
        array $fields
    ): void {
        foreach ($fields as $field) {
            if (
                !array_key_exists($field, $payload) ||
                $payload[$field] === null ||
                $payload[$field] === ''
            ) {
                throw new BetnexException(
                    "{$field} is required"
                );
            }
        }
    }

    private function request(callable $callback)
    {
        $lastError = null;

        for (
            $attempt = 1;
            $attempt <= $this->config['retries'];
            $attempt++
        ) {
            try {
                return $callback();
            } catch (\Throwable $error) {

                $lastError = $error;

                if ($this->config['debug']) {
                    error_log(
                        "[Betnex SDK] Attempt {$attempt} failed: "
                        . $error->getMessage()
                    );
                }

                if (
                    $attempt <
                    $this->config['retries']
                ) {
                    $this->sleep(
                        $attempt * 1000
                    );
                }
            }
        }

        throw $lastError;
    }

    public function getProviders(): array
    {
        return $this->request(
            function () {

                $response =
                    $this->client->get(
                        Constants::ENDPOINTS[
                            'PROVIDERS'
                        ]
                    );

                return json_decode(
                    $response->getBody()
                        ->getContents(),
                    true
                );
            }
        );
    }

    public function getGames(
        string $provider
    ): array {

        if (empty($provider)) {
            throw new BetnexException(
                "provider must be a valid string"
            );
        }

        return $this->request(
            function () use ($provider) {

                $response =
                    $this->client->get(
                        Constants::ENDPOINTS[
                            'GAMES'
                        ],
                        [
                            'query' => [
                                'provider'
                                    => $provider
                            ]
                        ]
                    );

                return json_decode(
                    $response->getBody()
                        ->getContents(),
                    true
                );
            }
        );
    }

    public function launchGame(
        array $payload = []
    ): array {

        $this->validateRequired(
            $payload,
            [
                "username",
                "gameId",
                "money",
                "platform",
                "home_url"
            ]
        );

        if (
            !is_string(
                $payload["username"]
            )
        ) {
            throw new BetnexException(
                "username must be a string"
            );
        }

        if (
            !is_string(
                $payload["gameId"]
            )
        ) {
            throw new BetnexException(
                "gameId must be a string"
            );
        }

        if (
            !is_numeric(
                $payload["money"]
            )
        ) {
            throw new BetnexException(
                "money must be numeric"
            );
        }

        if (
            !$this->isValidUrl(
                $payload["home_url"]
            )
        ) {
            throw new BetnexException(
                "home_url must be a valid URL"
            );
        }

        $requestPayload = [
            "username"
                => $payload["username"],

            "gameId"
                => $payload["gameId"],

            "money"
                => (float)$payload["money"],

            "platform"
                => $payload["platform"],

            "home_url"
                => $payload["home_url"],

            "lang"
                => $payload["lang"] ?? null
        ];

        if (
            !empty(
                $payload["currency"]
            )
        ) {
            $requestPayload["currency"] =
                $payload["currency"];
        }

        if ($this->config["debug"]) {
            error_log(
                "[Betnex SDK] Payload: "
                . json_encode(
                    $requestPayload
                )
            );
        }

        return $this->request(
            function () use (
                $requestPayload
            ) {

                $response =
                    $this->client->post(
                        Constants::ENDPOINTS[
                            'GAME_URL'
                        ],
                        [
                            'json'
                                => $requestPayload
                        ]
                    );

                return json_decode(
                    $response->getBody()
                        ->getContents(),
                    true
                );
            }
        );
    }

    public function getConfig(): array
    {
        $config =
            $this->config;

        $config["apiKey"] =
            "***hidden***";

        return $config;
    }

    public function setHeaderName(
        string $headerName
    ): void {

        if (
            !in_array(
                $headerName,
                self::SUPPORTED_HEADERS
            )
        ) {
            throw new BetnexException(
                'Unsupported header name. Supported values: '
                . implode(
                    ', ',
                    self::SUPPORTED_HEADERS
                )
            );
        }

        $this->config["headerName"] =
            $headerName;

        $this->client =
            new Client([
                'base_uri'
                    => $this->config['baseUrl'],

                'timeout'
                    => $this->config['timeout'] / 1000,

                'headers' => [
                    $headerName
                        => $this->apiKey,

                    'Content-Type'
                        => 'application/json',

                    'Accept'
                        => 'application/json'
                ]
            ]);
    }
}