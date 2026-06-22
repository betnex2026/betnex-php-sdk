<?php

namespace Betnex\Utils;

class CalculateBalance
{
    public static function calculate(
        float|int $currentBalance,
        float|int $betAmount,
        float|int $winAmount
    ): float {
        return
            (float)$currentBalance -
            (float)$betAmount +
            (float)$winAmount;
    }
}