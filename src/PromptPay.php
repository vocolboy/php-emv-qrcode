<?php

namespace Vocolboy\PromptpayGenerator;

class PromptPay
{
    public static function generate(string $promptpayId, ?float $amount = 0): string
    {
        $promptpayIdLength = strlen($promptpayId);
        $promptpayIdCode = match (true) {
            $promptpayIdLength >= 15 => '03',
            $promptpayIdLength >= 13 => '02',
            default => '01'
        };

        $data = [
            EMV::calculateString('00', '01'),
            EMV::calculateString('01', $amount ? '12' : '11'),
            EMV::calculateString(
                '29',
                EMV::serialize([
                    EMV::calculateString('00', 'A000000677010111'),
                    EMV::calculateString($promptpayIdCode, self::formatPromptpayId($promptpayId)),
                ])
            ),
            EMV::calculateString("58", "TH"),
            EMV::calculateString("53", "764"),
        ];

        if ($amount) {
            $data[] = EMV::calculateString("54", number_format($amount, 2, '.', ''));
        }

        $data[] = EMV::calculateString(63, EMV::crc16($data));

        return EMV::serialize($data);
    }

    public static function formatPromptpayId(string $promptpayId): string
    {
        $promptpayId = preg_replace('/[^0-9]/', '', $promptpayId);
        $zeroPadPromptpayId = str_pad(preg_replace('/^0/', '66', $promptpayId), 13, '0', STR_PAD_LEFT);

        return strlen($promptpayId) > 13 ? $promptpayId : $zeroPadPromptpayId;
    }
}