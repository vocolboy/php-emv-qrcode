<?php

use Vocolboy\PromptpayGenerator\PromptPay;

test('generate without amount', function () {
    expect(PromptPay::generate('0912345678'))->toEqual('00020101021129370016A000000677010111011300669123456785802TH53037646304F1E3');
});

test('generate with amount 100.88', function () {
    expect(PromptPay::generate('0912345678', 100.88))->toEqual('00020101021229370016A000000677010111011300669123456785802TH53037645406100.8863049DA4');
});

test('generate with amount 111.111', function () {
    expect(PromptPay::generate('0912345678', 111.111))->toEqual('00020101021229370016A000000677010111011300669123456785802TH53037645406111.116304D1CA');
});

test('generate with amount 100', function () {
    expect(PromptPay::generate('0912345678', 100))->toEqual('00020101021229370016A000000677010111011300669123456785802TH53037645406100.00630492CB');
});