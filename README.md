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

# Utils
```php
echo EMV::generatePHPDataByQRCode("00020101021229370016A000000677010111011300669123456785802TH53037645406100.00630492CB");

#output
string(506) "$data = [
    EMV::calculateString('00', '01'),
    EMV::calculateString('01', '12'),
    EMV::calculateString(
        '29',
        EMV::serialize([
            EMV::calculateString('00', 'A000000677010111'),
            EMV::calculateString('01', '0066912345678'),
        ])
    ),
    EMV::calculateString('58', 'TH'),
    EMV::calculateString('53', '764'),
    EMV::calculateString('54', '100.00'),
    EMV::calculateString('63', '92CB'),
];

$data[] = EMV::calculateString('63', EMV::crc16($data));"
```

# Testing
```shell
./vendor/bin/pest

#docker
docker run -it --rm -v $(pwd):/root -w /root sineverba/php8xc:1.7.0 composer install
```
