<?php

namespace OAuth\Common\Consumer;

/**
 * Value object for the credentials of an OAuth service.
 */
class ShopifyCredentials implements CredentialsInterface
{
    /**
     * @var string
     */
    protected $consumerId;

    /**
     * @var string
     */
    protected $consumerSecret;

    /**
     * @var string
     */
    protected $callbackUrl;

     /**
     * @var string
     */
    protected $shopDomain;

    /**
     * @param string $consumerId
     * @param string $consumerSecret
     * @param string $callbackUrl
     */
    public function __construct($consumerId, $consumerSecret, $callbackUrl, $shopDomain)
    {
        $this->consumerId = $consumerId;
        $this->consumerSecret = $consumerSecret;
        $this->callbackUrl = $callbackUrl;
        $this->shopDomain = $shopDomain;

        if(filter_var($shopDomain, FILTER_VALIDATE_URL))
        {
            $this->shopDomain = parse_url($shopDomain,PHP_URL_HOST);
        }
    }

    /**
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * @return string
     */
    public function getConsumerId()
    {
        return $this->consumerId;
    }

    /**
     * @return string
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * @return string
     */
    public function getShopDomain()
    {
        return $this->shopDomain;
    }
}
