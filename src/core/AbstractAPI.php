<?php

namespace winwin\mbupay\core;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use winwin\mbupay\Config;
use winwin\mbupay\support\Collection;
use winwin\mbupay\support\Util;
use winwin\mbupay\support\XML;

abstract class AbstractAPI implements LoggerAwareInterface
{
    /**
     * Http instance.
     *
     * @var Http
     */
    protected $http;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->getHttp()->setLogger($logger);
    }

    /**
     * 获取接口配置.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 获取 Http 客户端.
     *
     * @return Http
     */
    public function getHttp()
    {
        if ($this->http === null) {
            $this->setHttp(new Http());
        }

        return $this->http;
    }

    /**
     * 设置 Http 客户端.
     *
     * @param Http
     *
     * @return static
     */
    public function setHttp(Http $http)
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Make a API request.
     *
     * @param string $api
     * @param array  $params
     * @param string $method
     * @param array  $options
     * @param bool   $returnResponse
     *
     * @return Collection|\Psr\Http\Message\ResponseInterface
     */
    protected function request(array $params, $method = 'post', array $options = [], $returnResponse = false)
    {
        $params = array_merge($params, $this->config->only(['appid', 'charset', 'sign_type', 'version', 'mch_id']));

        $params['nonce_str'] = uniqid();
        $params = array_filter($params, function ($val) {
            return isset($val) && $val !== '';
        });
        $params['sign'] = Util::generateSign($params, $this->config->secret, $this->config->sign_type ?: 'md5');

        $options = array_merge([
            'body' => XML::build($params),
        ], $options);

        $response = $this->getHttp()->request($method, $this->config->gateway, $options);

        return $returnResponse ? $response : $this->parseResponse($response);
    }

    /**
     * Parse Response XML to array.
     *
     * @param ResponseInterface $response
     *
     * @return Collection
     */
    protected function parseResponse($response)
    {
        if ($response instanceof ResponseInterface) {
            $response = $response->getBody();
        }

        return new Collection((array) XML::parse($response));
    }
}
