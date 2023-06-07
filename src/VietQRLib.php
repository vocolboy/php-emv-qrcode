<?php

namespace Vocolboy\PromptpayGenerator;

class VietQRLib
{
    #TODO without amount/memo
    public static function generate(string $bankCode, string $payeeAccount, string $amount, string $memo): string
    {
        $data = [
            EMV::calculateString('00', '01'),
            EMV::calculateString('01', '12'),
            EMV::calculateString(
                '38',
                EMV::serialize([
                    EMV::calculateString('00', 'A000000727'),
                    EMV::calculateString(
                        '01',
                        EMV::serialize([
                            EMV::calculateString('00', $bankCode),
                            EMV::calculateString('01', $payeeAccount),
                        ])
                    ),
                    EMV::calculateString('02', 'QRIBFTTA'), //card => QRIBFTTC, account => QRIBFTTA
                ])
            ),
            EMV::calculateString('53', '704'),
            EMV::calculateString('54', number_format($amount, 0, '.', '')),
            EMV::calculateString('58', 'VN'),
            EMV::calculateString(
                '62',
                EMV::serialize([
                    EMV::calculateString('08', $memo),
                ])
            ),
        ];

        $data[] = EMV::calculateString(63, EMV::crc16($data));

        return EMV::serialize($data);
    }
}
