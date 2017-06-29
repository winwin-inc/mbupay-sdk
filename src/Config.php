<?php

namespace winwin\mbupay;

use winwin\mbupay\support\Attribute;

class Config extends Attribute
{
    const VERSION = '2.0.1';
    const GATEWAY = 'https://api.citic.mbupay.com/pay/gateway';

    protected $attributes = [
        'gateway',
        'version',
        'appid',
        'secret',
        'charset',
        'mch_id',
        'sign_type',
    ];

    protected $requirements = [
        'gateway',
        'version',
        'appid',
        'secret',
        'mch_id',
    ];

    public function __construct(array $config)
    {
        parent::__construct(array_merge([
            'version' => self::VERSION,
            'gateway' => self::GATEWAY,
        ], $config));
    }
}
