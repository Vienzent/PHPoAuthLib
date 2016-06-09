<?php

namespace OAuth\Unit\Common\Consumer;

use OAuth\Common\Consumer\ShopifyCredentials;

class ShopifyCredentialsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\Common\Consumer\ShopifyCredentials::__construct
     */
    public function testConstructCorrectInterface()
    {
        $credentials = new ShopifyCredentials('foo', 'bar', 'baz','qux');

        $this->assertInstanceOf('\\OAuth\\Common\\Consumer\\CredentialsInterface', $credentials);
    }

    /**
     * @covers OAuth\Common\Consumer\ShopifyCredentials::__construct
     * @covers OAuth\Common\Consumer\ShopifyCredentials::getConsumerId
     */
    public function testGetConsumerId()
    {
        $credentials = new ShopifyCredentials('foo', 'bar', 'baz', 'qux');

        $this->assertSame('foo', $credentials->getConsumerId());
    }

    /**
     * @covers OAuth\Common\Consumer\ShopifyCredentials::__construct
     * @covers OAuth\Common\Consumer\ShopifyCredentials::getConsumerSecret
     */
    public function testGetConsumerSecret()
    {
        $credentials = new ShopifyCredentials('foo', 'bar', 'baz', 'qux');

        $this->assertSame('bar', $credentials->getConsumerSecret());
    }

    /**
     * @covers OAuth\Common\Consumer\ShopifyCredentials::__construct
     * @covers OAuth\Common\Consumer\ShopifyCredentials::getCallbackUrl
     */
    public function testGetCallbackUrl()
    {
        $credentials = new ShopifyCredentials('foo', 'bar', 'baz', 'qux');

        $this->assertSame('baz', $credentials->getCallbackUrl());
    }

    /**
     * @covers OAuth\Common\Consumer\ShopifyCredentials::__construct
     * @covers OAuth\Common\Consumer\ShopifyCredentials::getShopDomain
     */
    public function testGetShopDomain()
    {
        $credentials = new ShopifyCredentials('foo', 'bar', 'baz', 'qux');

        $this->assertSame('qux', $credentials->getShopDomain());
    }
}
