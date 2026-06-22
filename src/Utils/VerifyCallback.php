<?php

namespace Betnex\Utils;

use Betnex\Exceptions\BetnexException;

class VerifyCallback
{
    public static function verify(
        array $payload,
        ?string $expectedApiKey = null
    ): array {
        if (empty($payload)) {
            throw new BetnexException(
                "Callback payload is required"
            );
        }

        $requiredFields = [
            "bet_amount",
            "win_amount",
            "member_account",
            "game_uid",
            "game_round",
            "serial_number",
            "currency_code",
            "api_key",
            "game_name",
            "game_provider"
        ];

        foreach ($requiredFields as $field) {
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

        if (!is_numeric($payload["bet_amount"])) {
            throw new BetnexException(
                "bet_amount must be numeric"
            );
        }

        if (!is_numeric($payload["win_amount"])) {
            throw new BetnexException(
                "win_amount must be numeric"
            );
        }

        $stringFields = [
            "member_account",
            "game_uid",
            "game_round",
            "serial_number",
            "currency_code",
            "api_key",
            "game_name",
            "game_provider"
        ];

        foreach ($stringFields as $field) {
            if (!is_string($payload[$field])) {
                throw new BetnexException(
                    "{$field} must be a string"
                );
            }
        }

        if (
            $expectedApiKey !== null &&
            (string)$payload["api_key"] !== (string)$expectedApiKey
        ) {
            throw new BetnexException(
                "Invalid callback api_key"
            );
        }

        return array_merge(
            [
                "valid" => true
            ],
            $payload
        );
    }
}