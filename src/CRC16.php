<?php

namespace Vocolboy\PromptpayGenerator;

use JetBrains\PhpStorm\Pure;

class CRC16
{
    #[Pure]
    public static function xmodem($str, $initValue = 0): int
    {
        return self::hash($str, 0x1021, $initValue, 0);
    }

    private static function reverseChar($char): string
    {
        $byte = ord($char);
        $tmp = 0;
        for ($i = 0; $i < 8; ++$i) {
            if ($byte & (1 << $i)) {
                $tmp |= (1 << (7 - $i));
            }
        }

        return chr($tmp);
    }

    #[Pure]
    private static function reverseString($str)
    {
        $m = 0;
        $n = strlen($str) - 1;
        while ($m <= $n) {
            if ($m == $n) {
                $str[$m] = self::reverseChar($str[$m]);
                break;
            }
            $ord1 = self::reverseChar($str[$m]);
            $ord2 = self::reverseChar($str[$n]);
            $str[$m] = $ord2;
            $str[$n] = $ord1;
            $m++;
            $n--;
        }

        return $str;
    }

    #[Pure]
    public static function hash(
        $str,
        $polynomial,
        $initValue,
        $xOrValue,
        $inputReverse = false,
        $outputReverse = false
    ): int {
        $crc = $initValue;

        for ($i = 0; $i < strlen($str); $i++) {
            if ($inputReverse) {
                $c = ord(self::reverseChar($str[$i]));
            } else {
                $c = ord($str[$i]);
            }
            $crc ^= ($c << 8);
            for ($j = 0; $j < 8; ++$j) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) & 0xffff) ^ $polynomial;
                } else {
                    $crc = ($crc << 1) & 0xffff;
                }
            }
        }
        if ($outputReverse) {
            $ret = pack('cc', $crc & 0xff, ($crc >> 8) & 0xff);
            $ret = self::reverseString($ret);
            $arr = unpack('vshort', $ret);
            $crc = $arr['short'];
        }

        return $crc ^ $xOrValue;
    }
}