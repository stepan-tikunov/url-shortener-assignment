<?php

namespace assignment\encode;

use assignment\Config;
use Katoni\FFX\Codecs\Text as FFX;

/**
 * Encodes number into base62 string using Feistel Cipher
 */
class FormatPreservingEncoder
{
    private FFX $encoder;
    private int $length;

    public function __construct() {
        $config = Config::getInstance();

        $this->length = $config->getInteger("url_length");

        $this->encoder = new FFX(
            $config->getString("ffx_key"),
            AlphanumericEncoder::ALPHABET,
            $this->length
        );
    }

    public function encode(int $value): string {
        return $this->encoder->encrypt(
            AlphanumericEncoder::encode($value, $this->length)
        );
    }

    public function decode(string $encoded): int {
        return AlphanumericEncoder::decode($this->encoder->decrypt($encoded));
    }
}
