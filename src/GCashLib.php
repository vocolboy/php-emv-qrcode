<?php

namespace Vocolboy\PromptpayGenerator;

class GCashLib
{
    public static function generate(
        string $payeeUserId,
        string $payeeName,
        ?string $amount = null,
        ?string $location = 'MALANDAY'
    ): string {
        $data = [
            EMV::calculateString('00', '01'),
            EMV::calculateString('01', $amount ? '12' : '11'),
            EMV::calculateString(
                '27',
                EMV::serialize([
                    EMV::calculateString('00', 'com.p2pqrpay'),
                    EMV::calculateString('01', 'GXCHPHM2XXX'),
                    EMV::calculateString('02', '99964403'),
                    EMV::calculateString('03', '217020000000656'),
                    EMV::calculateString('04', $payeeUserId),
                ])
            ),
            EMV::calculateString('52', '6016'),
            EMV::calculateString('53', '608'),
            $amount ? EMV::calculateString('54', number_format($amount, 2, '.', '')) : null,
            EMV::calculateString('58', 'PH'),
            EMV::calculateString('59', $payeeName),
            EMV::calculateString('60', $location),
            EMV::calculateString('61', '1234'),
        ];

        $data[] = EMV::calculateString(63, EMV::crc16($data));

        return EMV::serialize($data);
    }
}