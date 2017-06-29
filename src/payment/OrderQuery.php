<?php

namespace winwin\mbupay\payment;

use winwin\mbupay\support\Attribute;

class OrderQuery extends Attribute
{
    protected $attributes = [
        'method',
        'transaction_id',
        'pass_trade_no',
        'out_trade_no',
        'out_refund_no',
        'pass_refund_no',
        'refund_id',
    ];

    protected $requirements = [
        'method',
    ];
}
