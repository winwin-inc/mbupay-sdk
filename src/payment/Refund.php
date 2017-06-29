<?php

namespace winwin\mbupay\payment;

use winwin\mbupay\support\Attribute;

class Refund extends Attribute
{
    protected $attributes = [
        'method',
        'device_info',
        'transaction_id',
        'pass_trade_no',
        'out_trade_no',
        'out_refund_no',
        'total_fee',
        'refund_fee',
        'refund_fee_type',
        'op_user_id',
    ];

    protected $requirements = [
        'method',
        'out_refund_no',
        'total_fee',
        'refund_fee',
    ];
}
