<?php

namespace Vocolboy\PromptpayGenerator;

class PromptPay
{
    /**
     * @param string $promptpayId
     * @param float|null $amount
     * @return string
     */
    public static function generate(string $promptpayId, ?float $amount = 0): string
    {
        $promptpayIdLength = strlen($promptpayId);
        $promptpayIdCode = match (true) {
            $promptpayIdLength >= 15 => '03',
            $promptpayIdLength >= 13 => '02',
            default => '01'
        };

        $data = [
            PromptPayLib::calculateString('00', '01'),
            PromptPayLib::calculateString('01', $amount ? '12' : '11'),
            PromptPayLib::calculateString('29', PromptPayLib::serialize([
                PromptPayLib::calculateString('00', 'A000000677010111'),
                PromptPayLib::calculateString($promptpayIdCode, PromptPayLib::formatPromptpayId($promptpayId)),
            ])),
            PromptPayLib::calculateString("58", "TH"),
            PromptPayLib::calculateString("53", "764"),
        ];

        if ($amount) {
            $data[] = PromptPayLib::calculateString("54", number_format($amount, 2,'.',''));
        }

        $crc16 = CRC16::xmodem(PromptPayLib::serialize($data).'6304', 65535);
        $crc16 = str_pad(strtoupper(dechex($crc16)), 4, '0', STR_PAD_LEFT);
        $data[] = PromptPayLib::calculateString(63, $crc16);

        return PromptPayLib::serialize($data);
    }
}