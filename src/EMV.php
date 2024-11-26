<?php

namespace Vocolboy\PromptpayGenerator;

class EMV
{

    // EMV QR Code Tag 描述對應表
    // 資料來源: W3C (https://www.w3.org/2020/Talks/emvco-qr-20201021.pdf)
    private static array $tagDescriptions = [
        '00' => '格式指標',
        '01' => '啟動方式',
        '02' => '商戶帳號信息',
        '52' => '商戶類別碼',
        '53' => '交易貨幣',
        '54' => '交易金額',
        '58' => '國家代碼',
        '59' => '商戶名稱',
        '60' => '商戶城市',
        '62' => '商戶信息模板',
        '63' => '校驗碼',
        // 嵌套子標籤
        '00-38' => '全球唯一標識符',
        '01-38' => '商戶帳號信息',
        '02-38' => '支付網絡名稱',
        '01-62' => '賬單號碼',
        '02-62' => '手機號碼',
        '03-62' => '商店標籤',
        '04-62' => '忠誠度號碼',
        '05-62' => '參考編號',
        '06-62' => '客戶數據',
        '07-62' => '終端 ID',
        '08-62' => '交易用途',
        '09-62' => '額外用戶數據請求',
    ];

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

    public static function decode(string $qrText, string $parentTag = ''): array
    {
        $result = [];
        $position = 0;

        while ($position < strlen($qrText)) {
            // 提取 Tag (前兩位)
            $tag = substr($qrText, $position, 2);
            $position += 2;

            // 提取 Length (後兩位)
            $length = intval(substr($qrText, $position, 2));
            $position += 2;

            // 提取 Value (根據長度)
            $value = substr($qrText, $position, $length);
            $position += $length;

            // 如果是嵌套值 (例如 Tag 38 和 62)
            if (in_array($tag, ['38', '62'])) {
                $value = self::decode($value, $tag); // 遞歸解析嵌套結構
            }

            // 拼接父標籤與子標籤（如果存在父標籤）
            $descriptionKey = $parentTag ? "{$tag}-{$parentTag}" : $tag;

            // 添加到結果數組
            $result[$tag] = [
                'value' => $value,
                'description' => self::$tagDescriptions[$descriptionKey] ?? '未知字段',
            ];
        }

        return $result;
    }
}
