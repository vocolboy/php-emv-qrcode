<?php

use Vocolboy\PromptpayGenerator\GCashLib;

test('generate without amount', function () {
    expect(
        GCashLib::generate(payeeUserId: 'DWQM4TK3JDNYH9CHA', payeeName: 'AN***A%20C')
    )->toEqual(
        '00020101021127830012com.p2pqrpay0111GXCHPHM2XXX02089996440303152170200000006560417DWQM4TK3JDNYH9CHA5204601653036085802PH5910AN***A%20C6008MALANDAY6104123463047F32'
    );
});

test('generate with amount 666', function () {
    expect(
        GCashLib::generate(
            payeeUserId: 'DWQM4TK3JDO26GF27',
            payeeName: 'Y* CH*N S.',
            amount: '666',
            location: 'Agong-ong'
        )
    )->toEqual(
        '00020101021227830012com.p2pqrpay0111GXCHPHM2XXX02089996440303152170200000006560417DWQM4TK3JDO26GF275204601653036085406666.005802PH5910Y* CH*N S.6009Agong-ong6104123463040D0A'
    );
});