<?php

namespace Vocolboy\PromptpayGenerator;

class EMV
{
    public static function calculateString(string $first, string $second): string
    {
        return implode('', [$first, substr('00' . strlen($second), -2), $second]);
    }

    public static function serialize(array $data): string
    {
        return implode('', $data);
    }

    public static function crc16(array $data): string
    {
        $crc16 = CRC16::xmodem(EMV::serialize($data) . '6304', 65535);
        return str_pad(strtoupper(dechex($crc16)), 4, '0', STR_PAD_LEFT);
    }
}