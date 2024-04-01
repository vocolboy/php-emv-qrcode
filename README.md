# php-emv-qrcode ![Packagist Downloads](https://img.shields.io/packagist/dt/vocolboy/php-emv-qrcode)

# Introduction
Support QRCode generator
- TH ( TrueMoney / PromptPay )
- VN ( VietQR )
- PH ( GCash / PayMaya )

# Install
```shell
composer require vocolboy/php-emv-qrcode
```

# Usage
```
$promptpayId = '0912345678';
$amount = '100';

echo PromptPay::generate($promptpayId, $amount);
//00020101021229370016A000000677010111011300669123456785802TH53037645406100.00630492CB

$gcashId = 'DWQM4TK3JDO26GF27'
echo GCashLib::generate(payeeUserId: $gcashId);
```

# Testing
```shell
./vendor/bin/pest
```
