<?php

namespace winwin\mbupay\payment;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use winwin\mbupay\Config;
use winwin\mbupay\core\AbstractAPI;
use winwin\mbupay\core\FaultException;
use winwin\mbupay\support\Collection;
use winwin\mbupay\support\Util;
use winwin\mbupay\support\XML;

class Payment extends AbstractAPI
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    public function getRequest()
    {
        if ($this->request === null) {
            $this->request = Util::createRequestFromGlobals();
        }

        return $this->request;
    }

    /**
     * Sets request.
     *
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Prepare order to pay.
     *
     * @param Order $order
     *
     * @return Collection
     */
    public function prepare(Order $order)
    {
        if (is_null($order->spbill_create_ip)) {
            $order->spbill_create_ip = Util::getServerIp();
        }

        return $this->request($order->all());
    }

    /**
     * Micro pay.
     *
     * @param Order $order
     *
     * @return Collection
     */
    public function microPay(Order $order)
    {
        if (is_null($order->spbill_create_ip)) {
            $order->spbill_create_ip = Util::getServerIp();
        }

        if ($order->method == API::ALIPAY_MICROPAY) {
            $order->add('scene', 'bar_code');
        }
        if (!$order->has('auth_code')) {
            throw new \InvalidArgumentException(" 'auth code' cannot be empty.");
        }

        return $this->request($order->all());
    }

    /**
     * Query order.
     *
     * @param OrderQuery $query
     *
     * @return Collection
     */
    public function query(OrderQuery $query)
    {
        $data = $query->only(['method', 'transaction_id', 'pass_trade_no', 'out_trade_no']);
        if (count($data) < 2) {
            throw new \InvalidArgumentException(" 'transaction_id', 'pass_trade_no', 'out_trade_no' cannot be all empty.");
        }

        return $this->request($data);
    }

    /**
     * Reverse order.
     *
     * @param OrderQuery $query
     *
     * @return Collection
     */
    public function reverse(OrderQuery $query)
    {
        $data = $query->only(['method', 'transaction_id', 'pass_trade_no', 'out_trade_no']);
        if (count($data) < 2) {
            throw new \InvalidArgumentException(" 'transaction_id', 'pass_trade_no', 'out_trade_no' cannot be all empty.");
        }

        return $this->request($data);
    }

    /**
     * Query refund.
     *
     * @param OrderQuery $query
     *
     * @return Collection
     */
    public function refundQuery(OrderQuery $query)
    {
        $data = $query->only(['method', 'transaction_id', 'pass_trade_no', 'out_trade_no']);
        if (count($data) < 2) {
            throw new \InvalidArgumentException(" 'transaction_id', 'pass_trade_no', 'out_trade_no' cannot be all empty.");
        }
        $data = $query->only(['refund_id', 'pass_refund_no', 'out_refund_no']);
        if (count($data) < 1) {
            throw new \InvalidArgumentException(" 'refund_id', 'pass_refund_no', 'out_refund_no' cannot be all empty.");
        }

        return $this->request($query->all());
    }

    /**
     * 退款.
     *
     * @param Refund $refund
     *
     * @return Collection
     */
    public function refund(Refund $refund)
    {
        return $this->request($refund->all());
    }

    public function handleNotify(callable $callback)
    {
        $notify = new Notify($this->config, $this->getRequest());

        if (!$notify->isValid()) {
            throw new FaultException('Invalid request payloads.', 400);
        }

        $notify = $notify->getNotify();
        $successful = $notify->get('result_code') === 'SUCCESS';

        $handleResult = call_user_func_array($callback, [$notify, $successful]);
        $result = [
            'version' => Config::VERSION,
        ];

        if ($handleResult === true) {
            $result['return_code'] = 'SUCCESS';
        } else {
            $result['return_code'] = 'FAIL';
            $result['return_msg'] = $handleResult;
        }

        return new Response(200, [], XML::build($result));
    }
}
