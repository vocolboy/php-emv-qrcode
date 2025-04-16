<?php

use Vocolboy\PromptpayGenerator\EMV;
use Vocolboy\PromptpayGenerator\PromptPay;

test('calculate string case 1', function () {
    expect(EMV::calculateString('00', '01'))->toEqual('000201');
});

test('calculate string case 2', function () {
    expect(EMV::calculateString('54', '1000.01'))->toEqual('54071000.01');
});

test('serialize', function () {
    expect(EMV::serialize(['a', 'b', 'c']))->toEqual('abc');
});

test('when function true', function () {
    $amount = 1000;
    expect(EMV::when($amount, fn() => EMV::calculateString('54', $amount)))->toEqual('54041000');
});

test('when function false', function () {
    $amount = null;
    expect(EMV::when($amount, fn() => EMV::calculateString('54', $amount)))->toEqual(null);
});

test('decode', function () {
    expect(
        EMV::decode(
            "00020101021227830012com.p2pqrpay0111GXCHPHM2XXX02089996440303152170200000006560417DWQM4TK3JDO26GF275204601653036085406666.005802PH5910Y* CH*N S.6009Agong-ong6104123463040D0A"
        )
    )->toEqual([
        "00" => "01",
        "01" => "12",
        "27" => [
            "00" => "com.p2pqrpay",
            "01" => "GXCHPHM2XXX",
            "02" => "99964403",
            "03" => "217020000000656",
            "04" => "DWQM4TK3JDO26GF27"
        ],
        "52" => "6016",
        "53" => "608",
        "54" => "666.00",
        "58" => "PH",
        "59" => "Y* CH*N S.",
        "60" => "Agong-ong",
        "61" => "1234",
        "63" => "0D0A"
    ]);
});


test('generate php by qrcode', function () {
    expect(
        EMV::generatePHPDataByQRCode(
            "00020101021238630010A00000072701330006970422011997042292087067309020208QRIBFTTC530370454061000005802VN621502118432917204163047793"
        )
    )->toEqual(
        "\$data = [
    EMV::calculateString('00', '01'),
    EMV::calculateString('01', '12'),
    EMV::calculateString(
        '38',
        EMV::serialize([
            EMV::calculateString('00', 'A000000727'),
            EMV::calculateString('01', '000697042201199704229208706730902'),
            EMV::calculateString('02', 'QRIBFTTC'),
        ])
    ),
    EMV::calculateString('53', '704'),
    EMV::calculateString('54', '100000'),
    EMV::calculateString('58', 'VN'),
    EMV::calculateString(
        '62',
        EMV::serialize([
            EMV::calculateString('02', '84329172041'),
        ])
    ),
    EMV::calculateString('63', '7793'),
];

\$data[] = EMV::calculateString('63', EMV::crc16(\$data));\n"
    );
});

test('replace or add multiple values', function () {
    expect(
        EMV::replaceOrAddValues(
            "00020101021128500011ph.ppmi.p2m0111PAPHPHM1XXX030920143184005033105204539953036085802PH5916Jverlin Contrano6004Naic62440014com.paymaya.qr0522RCjLbs2j7swGVvnBuZGXtL63049320",
            [
                '01' => '12',
                '54' => '100.00'
            ]
        )
    )->toEqual(
        "00020101021228500011ph.ppmi.p2m0111PAPHPHM1XXX030920143184005033105204539953036085406100.005802PH5916Jverlin Contrano6004Naic62440014com.paymaya.qr0522RCjLbs2j7swGVvnBuZGXtL63040430"
    );
});

test('replace or add multiple values 2', function () {
    expect(
        EMV::replaceOrAddValues(
            "00020101021228500011ph.ppmi.p2m0111PAPHPHM1XXX030920144830105030115204539953036085406122.995802PH5908314620916005Pasig62440014com.paymaya.qr0522TZ2qmKifcHwxgiNrwXXm8L6304A531",
            [
                '54' => '101.00'
            ]
        )
    )->dd()->toEqual(
        "00020101021228500011ph.ppmi.p2m0111PAPHPHM1XXX030920144830105030115204539953036085406101.005802PH5908314620916005Pasig62440014com.paymaya.qr0522TZ2qmKifcHwxgiNrwXXm8L6304EF75"
    );
});