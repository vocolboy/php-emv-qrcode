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

    public static function when($condition, callable $value)
    {
        return $condition ? $value() : null;
    }

    public static function decode(string $data): array
    {
        $offset = 0;
        return self::parse($data, $offset);
    }

    private static function parse(string $data, &$offset): array
    {
        $result = [];
        $length = strlen($data);

        while ($offset < $length) {
            $id = substr($data, $offset, 2);
            $offset += 2;

            $len = substr($data, $offset, 2);
            $offset += 2;
            $len = intval($len);

            $value = substr($data, $offset, $len);
            $offset += $len;

            if (self::needsSubParsing($id)) {
                $sub_offset = 0;
                $value = self::parse($value, $sub_offset);
            }

            // Add to result
            $result[$id] = $value;
        }

        return $result;
    }

    private static function needsSubParsing(string $id): bool
    {
        $int_id = intval($id);
        return ($int_id >= 26 && $int_id <= 51) || $int_id == 62;
    }

    public static function replaceOrAddValues(string $dataString, array $fieldsToAddOrReplace): string
    {
        $decodedData = self::decode($dataString);

        unset($decodedData['63']);

        foreach ($fieldsToAddOrReplace as $id => $value) {
            if ($id === '63') {
                continue;
            }

            $decodedData[$id] = $value;
        }

        $reconstructedFields = [];
        ksort($decodedData);
        foreach ($decodedData as $currentId => $currentValue) {
            if (is_array($currentValue)) {
                $nestedSerialized = self::serializeNestedArray($currentValue);
                $reconstructedFields[] = self::calculateString($currentId, $nestedSerialized);
            } else {
                $reconstructedFields[] = self::calculateString($currentId, (string)$currentValue);
            }
        }

        usort($reconstructedFields, function ($a, $b) {
            $idA = substr($a, 0, 2);
            $idB = substr($b, 0, 2);
            return intval($idA) <=> intval($idB);
        });

        $newCrc = self::crc16($reconstructedFields);

        $reconstructedFields[] = self::calculateString('63', $newCrc);

        return self::serialize($reconstructedFields);
    }

    private static function serializeNestedArray(array $nestedData): string
    {
        $nestedParts = [];
        foreach ($nestedData as $nestedId => $nestedValue) {
            if (is_array($nestedValue)) {
                $deepNestedSerialized = self::serializeNestedArray($nestedValue);
                $nestedParts[] = self::calculateString($nestedId, $deepNestedSerialized);
            } else {
                $nestedParts[] = self::calculateString($nestedId, (string)$nestedValue);
            }
        }

        usort($nestedParts, function ($a, $b) {
            $idA = substr($a, 0, 2);
            $idB = substr($b, 0, 2);
            return intval($idA) <=> intval($idB);
        });

        return self::serialize($nestedParts);
    }

    public static function generatePHPDataByQRCode(string $data): string
    {
        $struct = self::decode($data);
        $code = "\$data = [\n";
        $code .= self::generateCodeArray($struct, 1);
        $code .= "];\n\n";
        $code .= "\$data[] = EMV::calculateString('63', EMV::crc16(\$data));\n";
        return $code;
    }

    private static function generateCodeArray(array $struct, int $indentLevel): string
    {
        $code = '';
        $indent = str_repeat('    ', $indentLevel);
        foreach ($struct as $key => $value) {
            if (is_array($value)) {
                // Handle nested structures
                $code .= "{$indent}EMV::calculateString(\n";
                $code .= "{$indent}    '{$key}',\n";
                $code .= "{$indent}    EMV::serialize([\n";
                $code .= self::generateCodeArray($value, $indentLevel + 2);
                $code .= "{$indent}    ])\n";
                $code .= "{$indent}),\n";
            } else {
                // Simple key-value pair
                $code .= "{$indent}EMV::calculateString('{$key}', '{$value}'),\n";
            }
        }
        return $code;
    }
}