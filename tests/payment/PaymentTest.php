<?php

namespace winwin\mbupay\payment;

use winwin\mbupay\Config;
use winwin\mbupay\TestCase;

class PaymentTest extends TestCase
{
    public function createPayment()
    {
        return new Payment(new Config([
            'appid' => getenv("CITIC_APP_ID"),
            'secret' => getenv("CITIC_MCH_SECRET"),
            'mch_id' => getenv("CITIC_MCH_ID"),
        ]));
    }

    public function testPrepare()
    {
        $result = $this->createPayment()->prepare(new Order([
            'method' => API::WXPAY_JSPAY,
            'out_trade_no' => uniqid(),
            'body' => 'test',
            'total_fee' => 1,
            'openid' => 'oynvnwN8e0we0u52IwftIfxqdbis', // openid就是该公众号下的openid
        ]));
        var_dump($result);
    }

    public function testMicroPay()
    {
        $result = $this->createPayment()->microPay(new Order([
            'method' => API::WXPAY_MICROPAY,
            'out_trade_no' => $outTradeNo = uniqid(),
            'body' => 'test',
            'total_fee' => 1,
            'auth_code' => '130349739808959724',
        ]));
        var_dump($outTradeNo);
        var_dump($result); // 支付成功返回了openid
    }

    public function testQuery()
    {
        $result = $this->createPayment()->query(new OrderQuery([
            'method' => API::WXPAY_QUERY,
            'out_trade_no' => '59365f0d8bcad',
        ]));
        var_dump($result); // 返回的openid是该公众号下的openid
    }

    public function testReverse()
    {
        $result = $this->createPayment()->reverse(new OrderQuery([
            'method' => API::WXPAY_REVERSE,
            'out_trade_no' => '5936642151406',
        ]));
        var_dump($result);
    }

    public function testRefund()
    {
        $result = $this->createPayment()->refund(new Refund([
            'method' => API::WXPAY_REFUND,
            'out_trade_no' => '593661b7b5e2b',
            'out_refund_no' => $outRefundNo = uniqid(),
            'total_fee' => 1,
            'refund_fee' => 1,
        ]));
        var_dump($outRefundNo);
        var_dump($result);
    }

    public function testRefundQuery()
    {
        $result = $this->createPayment()->refundQuery(new OrderQuery([
            'method' => API::WXPAY_REFUND_QUERY,
            'transaction_id' => '4005842001201706064553372651',
            'out_refund_no' => '59366ca97271c',
        ]));
        var_dump($result);
    }
}
