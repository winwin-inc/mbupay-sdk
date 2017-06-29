<?php

namespace winwin\mbupay\payment;

use Psr\Http\Message\ServerRequestInterface;
use winwin\mbupay\Config;
use winwin\mbupay\core\FaultException;
use winwin\mbupay\support\Collection;
use winwin\mbupay\support\Util;
use winwin\mbupay\support\XML;

/**
 * Class Notify.
 */
class Notify
{
    /**
     * Config instance.
     *
     * @var Config
     */
    protected $config;

    /**
     * Request instance.
     *
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * Payment notify (extract from XML).
     *
     * @var Collection
     */
    protected $notify;

    /**
     * Constructor.
     *
     * @param Config                 $merchant
     * @param ServerRequestInterface $request
     */
    public function __construct(Config $config, ServerRequestInterface $request)
    {
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * Validate the request params.
     *
     * @return bool
     */
    public function isValid()
    {
        $notify = $this->getNotify();
        $localSign = Util::generateSign($notify->except('sign')->all(), $this->config->secret, $notify->sign_type ?: 'md5');

        return $localSign === $notify->get('sign');
    }

    /**
     * Return the notify body from request.
     *
     * @return Collection
     *
     * @throws FaultException
     */
    public function getNotify()
    {
        if (!empty($this->notify)) {
            return $this->notify;
        }
        try {
            $xml = XML::parse((string) $this->request->getBody());
        } catch (\Throwable $t) {
            throw new FaultException('Invalid request XML: '.$t->getMessage(), 400);
        } catch (\Exception $e) {
            throw new FaultException('Invalid request XML: '.$e->getMessage(), 400);
        }

        if (!is_array($xml) || empty($xml)) {
            throw new FaultException('Invalid request XML.', 400);
        }

        return $this->notify = new Collection($xml);
    }
}
