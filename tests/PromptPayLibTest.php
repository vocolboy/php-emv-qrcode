<?php

use Vocolboy\PromptpayGenerator\PromptPayLib;

test('calculate string case 1', function () {
    expect(PromptPayLib::calculateString('00', '01'))->toEqual('000201');
});

test('calculate string case 2', function () {
    expect(PromptPayLib::calculateString('54', '1000.01'))->toEqual('54071000.01');
});

test('serialize', function () {
    expect(PromptPayLib::serialize(['a', 'b', 'c']))->toEqual('abc');
});

test('format Promptpay id', function () {
    expect(PromptPayLib::formatPromptpayId('0912345678'))->toEqual('0066912345678');
});

test('format Promptpay id case 2', function () {
    expect(PromptPayLib::formatPromptpayId('1234567890123'))->toEqual('1234567890123');
});

test('format Promptpay id case 3', function () {
    expect(PromptPayLib::formatPromptpayId('091-234-5678'))->toEqual('0066912345678');
});