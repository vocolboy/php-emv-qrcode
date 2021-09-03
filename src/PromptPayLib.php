<?php

namespace Vocolboy\PromptpayGenerator;

class PromptPayLib
{
    /**
     * @param string $first
     * @param string $second
     * @return string
     */
    public static function calculateString(string $first, string $second): string
    {
        return implode('', [$first, substr('00'.strlen($second), -2), $second]);
    }

    /**
     * @param array $data
     * @return string
     */
    public static function serialize(array $data): string
    {
        return implode('', $data);
    }

    /**
     * @param string $promptpayId
     * @return string
     */
    public static function formatPromptpayId(string $promptpayId): string
    {
        $promptpayId = preg_replace('/[^0-9]/', '', $promptpayId);
        $zeroPadPromptpayId = str_pad(preg_replace('/^0/', '66', $promptpayId), 13, '0', STR_PAD_LEFT);

        return strlen($promptpayId) > 13 ? $promptpayId : $zeroPadPromptpayId;
    }
}