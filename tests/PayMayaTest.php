<?php

use Vocolboy\PromptpayGenerator\PayMayaLib;

test('generate without amount', function () {
    expect(PayMayaLib::generate(payerPhone: '09060498741', payeeName: 'Bernabe Jr Agustin'))->toEqual(
        '00020101021127780012com.p2pqrpay0111PAPHPHM1XXX02089996440304126390604987410515+63-906-04987415204601653036085802PH5918Bernabe Jr Agustin6010Valenzuela63041475'
    );
});

test('generate with amount 50000', function () {
    expect(PayMayaLib::generate('09060498741', 'Bernabe Jr Agustin', '50000', null))->toEqual(
        '00020101021227780012com.p2pqrpay0111PAPHPHM1XXX02089996440304126390604987410515+63-906-0498741520460165303608540850000.005802PH5918Bernabe Jr Agustin6010Valenzuela63045349'
    );
});

test('generate with amount 100.81', function () {
    expect(PayMayaLib::generate(payerPhone: '09060498741', payeeName: 'Bernabe Jr Agustin', amount: 100.81))->toEqual(
        '00020101021227780012com.p2pqrpay0111PAPHPHM1XXX02089996440304126390604987410515+63-906-04987415204601653036085406100.815802PH5918Bernabe Jr Agustin6010Valenzuela6304C5AB'
    );
});

test('generate with amount 1000', function () {
    expect(
        PayMayaLib::generate(payerPhone: '09060498741', payeeName: 'Bernabe Jr Agustin', amount: 1000, memo: 'Meme')
    )->toEqual(
        '00020101021227780012com.p2pqrpay0111PAPHPHM1XXX02089996440304126390604987410515+63-906-049874152046016530360854071000.005802PH5918Bernabe Jr Agustin6010Valenzuela62080804Meme6304972E'
    );
});

test('format phone', function () {
    expect(PayMayaLib::formatPhone('09156211673'))->toEqual('639156211673');
});

test('format phone regular', function () {
    expect(PayMayaLib::formatPhone('09156211673', true))->toEqual('+63-915-6211673');
});

test('format phone id ', function () {
    expect(PayMayaLib::formatPhone('091-234-5678'))->toEqual('63912345678');
});