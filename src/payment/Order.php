<?php

namespace winwin\mbupay\payment;

use winwin\mbupay\support\Attribute;

class Order extends Attribute
{
    protected $attributes = [
        'method',
        'out_trade_no',
        'body',
        'store_appid',
        'store_name',
        'op_user',
        'fee_type',
        'detail',
        'openid',
        'total_fee',
        'spbill_create_ip',
        'notify_url',
        'auth_code',
        'device_info',
        'attach',
        'time_start',
        'time_expire',
        'goods_tag',
        'limit_pay',
        'product_id',
    ];

    protected $requirements = [
        'method',
        'out_trade_no',
        'body',
        'total_fee',
        'spbill_create_ip',
    ];
}
