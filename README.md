# promptpay-generator

# Introduction
Reverse QRCode generator logic from [Promptpay](https://play.google.com/store/apps/details?id=com.frontware.promptpay&hl=en&gl=US)

The Bank of Thailand [introduced a **PromptPay QRCode Standard**](https://thestandard.co/standardqrcode/) that works with most mobile banking apps in Thailand.

# Install
```shell
composer require vocolboy/promptpay-generator
```

# Usage
```
$promptpayId = '0912345678';
$amount = '100';

echo PromptPay::generate($promptpayId, $amount);
//00020101021229370016A000000677010111011300669123456785802TH53037645406100.00630492CB
```

# Testing
```shell
./vendor/bin/pest
```