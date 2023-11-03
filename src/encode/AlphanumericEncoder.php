<?php

namespace assignment\encode;

class AlphanumericEncoder
{
    public const ALPHABET = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    public const BASE = 62;

    public static function encode(int $decimal, int $length): string {
        $result = "";

        while ($decimal > 0) {
            $remainder = $decimal % self::BASE;
            $result = self::ALPHABET[$remainder] . $result;

            $decimal = intdiv($decimal, self::BASE);
        }

        return str_pad($result, $length, "0", STR_PAD_LEFT);
    }

    private static function decodeChar(string $char): int {
        return strpos(self::ALPHABET, $char);
    }

    public static function decode(string $alphanumeric): string {
        $result = 0;

        foreach (str_split($alphanumeric) as $c) {
            $result = $result * self::BASE + self::decodeChar($c);
        }

        return $result;
    }
}