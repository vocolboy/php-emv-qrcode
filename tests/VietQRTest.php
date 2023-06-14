<?php

use Vocolboy\PromptpayGenerator\VietQRLib;

test('generate with amount 10,000 memo 47464', function () {
    expect(
        VietQRLib::generate(
            '970418',
            '39010002560178',
            "10000.00",
            "47464"
        )
    )->toEqual(
        '00020101021238580010A000000727012800069704180114390100025601780208QRIBFTTA53037045405100005802VN620908054746463049F9F'
    );
});

test('generate with amount 12,345 memo 73095', function () {
    expect(
        VietQRLib::generate(
            '970436',
            '1031353245',
            "12345",
            "73095"
        )
    )->toEqual(
        '00020101021238540010A00000072701240006970436011010313532450208QRIBFTTA53037045405123455802VN620908057309563045EAF'
    );
});
