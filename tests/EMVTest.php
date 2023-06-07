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