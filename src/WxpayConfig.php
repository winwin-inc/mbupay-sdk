<?php

namespace winwin\mbupay;

use winwin\mbupay\core\AbstractAPI;

class WxpayConfig extends AbstractAPI
{
    public function setJsapiPath($url)
    {
        return $this->addConfig('jsapi_path', $url);
    }

    public function setAppid($appid)
    {
        return $this->addConfig('wx_appid', $appid);
    }

    public function setSubscribeAppid($appid)
    {
        return $this->addConfig('subscribe_appid', $appid);
    }

    private function addConfig($name, $value)
    {
        return $this->request([
            'method' => 'mbupay.wxpay.jsaddconf',
            $name => $value,
        ]);
    }

    public function getConfig()
    {
        return $this->request([
            'method' => 'mbupay.wxpay.jsconf',
        ]);
    }
}
