<?php

namespace Vocolboy\PromptpayGenerator;

class PayMayaLib
{
    public static function generate(
        string $payerPhone,
        string $payeeName,
        ?string $amount = null,
        ?string $memo = null
    ): string {
        $data = [
            EMV::calculateString('00', '01'),
            EMV::calculateString('01', $amount ? '12' : '11'),
            EMV::calculateString(
                '27',
                EMV::serialize([
                    EMV::calculateString('00', 'com.p2pqrpay'),
                    EMV::calculateString('01', 'PAPHPHM1XXX'),
                    EMV::calculateString('02', '99964403'),
                    EMV::calculateString('04', self::formatPhone($payerPhone)),
                    EMV::calculateString('05', self::formatPhone($payerPhone, true)),
                ])
            ),
            EMV::calculateString('52', '6016'),
            EMV::calculateString('53', '608'),
            $amount ? EMV::calculateString('54', number_format($amount, 2, '.', '')) : null,
            EMV::calculateString('58', 'PH'),
            EMV::calculateString('59', $payeeName),
            EMV::calculateString('60', 'Valenzuela'),
            $memo ? EMV::calculateString('62', EMV::serialize([EMV::calculateString('08', $memo)])) : null
        ];

        $data[] = EMV::calculateString(63, EMV::crc16($data));

        return EMV::serialize($data);
    }

    public static function formatPhone(string $payerPhone, bool $regular = false): string
    {
        $payerPhone = preg_replace('/[^0-9]/', '', $payerPhone);

        if (str_starts_with($payerPhone, '0')) {
            $prefix = ($regular) ? '+63' : '63';
            $payerPhone = $prefix . substr($payerPhone, 1);
        }

        if ($regular) {
            $pattern = '/(\d{2})(\d{3})(\d{6})/';
            $replacement = '$1-$2-$3';
            return preg_replace($pattern, $replacement, $payerPhone);
        } else {
            return $payerPhone;
        }
    }
}