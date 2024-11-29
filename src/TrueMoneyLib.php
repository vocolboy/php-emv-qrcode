<?php

namespace Vocolboy\PromptpayGenerator;

class TrueMoneyLib
{
    public static function generate(string $trueMoneyId, ?float $amount = null, ?string $memo = null): string
    {
        $trueMoneyId = '14000' . $trueMoneyId;

        $data = [
            EMV::calculateString('00', '01'),
            EMV::calculateString('01', $amount ? '12' : '11'),
            EMV::calculateString('29', self::calculatePromptPayIdData($trueMoneyId)),
            EMV::when(
                $amount,
                fn() => EMV::calculateString('54', number_format($amount, 2, '.', ''))
            ),
            EMV::calculateString('58', 'TH'),
            EMV::calculateString('53', '764'),
            EMV::when(
                $memo,
                fn() => EMV::calculateString('81', self::generateMemo($memo))
            ),
        ];

        $data[] = EMV::calculateString(63, EMV::crc16($data));

        return EMV::serialize($data);
    }

    private static function calculatePromptPayIdData(string $trueMoneyId): string
    {
        $trueMoneyIdLength = strlen($trueMoneyId);
        $trueMoneyIdCode = match (true) {
            $trueMoneyIdLength >= 15 => '03',
            $trueMoneyIdLength >= 13 => '02',
            default => '01'
        };

        return EMV::serialize(
            [
                EMV::calculateString('00', 'A000000677010111'),
                EMV::calculateString($trueMoneyIdCode, self::formatId($trueMoneyId)),
            ]
        );
    }

    private static function generateMemo($memo): string
    {
        $bin2hexMemo = '';

        foreach (str_split($memo) as $i) {
            $bin2hexMemo .= sprintf('%04d', bin2hex($i));
        }

        return $bin2hexMemo;
    }

    public static function formatId(string $trueMoneyId): string
    {
        $trueMoneyId = preg_replace('/[^0-9]/', '', $trueMoneyId);
        $zeroPadId = str_pad(preg_replace('/^0/', '66', $trueMoneyId), 13, '0', STR_PAD_LEFT);

        return strlen($trueMoneyId) > 13 ? $trueMoneyId : $zeroPadId;
    }
}
