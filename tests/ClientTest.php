<?php

namespace Moltin\SDK\Tests;

use Moltin;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    private $underTest;
    private $initialConfig = [
        'currency_code' => 'USD',
        'language' => 'EN',
        'locale' => 'EN_GB',
        'client_id' => 'abc',
        'client_secret' => '123',
        'api_endpoint' => 'https://api.moltin.com',
        'cookie_cart_name' => 'moltin_cart_name_for_cookie',
        'cookie_lifetime' => '+3 days'
    ];

    public function setUp()
    {
        $this->underTest = new Moltin\Client($this->initialConfig);
    }

    public function testSetUpConfiguresCurrency()
    {
        $this->assertEquals($this->initialConfig['currency_code'], $this->underTest->getCurrencyCode());
    }

    public function testSetUpConfiguresLanguage()
    {
        $this->assertEquals($this->initialConfig['language'], $this->underTest->getLanguage());
    }

    public function testSetUpConfiguresLocale()
    {
        $this->assertEquals($this->initialConfig['locale'], $this->underTest->getLocale());
    }

    public function testSetUpConfiguresClientID()
    {
        $this->assertEquals($this->initialConfig['client_id'], $this->underTest->getClientID());
    }

    public function testSetUpConfiguresClientSecret()
    {
        $this->assertEquals($this->initialConfig['client_secret'], $this->underTest->getClientSecret());
    }

    public function testSetUpConfiguresCookieName()
    {
        $this->assertEquals($this->initialConfig['cookie_cart_name'], $this->underTest->getCookieCartName());
    }

    public function testSetUpConfiguresCookieLifetime()
    {
        $this->assertEquals($this->initialConfig['cookie_lifetime'], $this->underTest->getCookieLifetime());
    }

    public function testCurrencySwitch()
    {
        $newCurrency = 'GBP';
        $this->underTest->setCurrencyCode($newCurrency);
        $this->assertEquals($newCurrency, $this->underTest->getCurrencyCode());
    }

    public function testCurrencyAlias()
    {
        $newCurrency = 'AUD';
        $this->underTest->currency($newCurrency);
        $this->assertEquals($newCurrency, $this->underTest->getCurrencyCode());
    }

    public function testGetVersion()
    {
        $this->assertEquals($this->underTest->getVersion(), 'v2');
    }

    public function testGetBase()
    {
        $this->assertEquals($this->underTest->getBase(), 'https://api.moltin.com');
    }

    public function testGetAuthURI()
    {
        $this->assertEquals($this->underTest->getAuthURI(), 'oauth/access_token');
    }

    public function testGetAuthEndpoint()
    {
        $this->assertEquals($this->underTest->getAuthEndpoint(), 'https://api.moltin.com/oauth/access_token');
    }

    public function testGetAPIEndpoint()
    {
        $this->assertEquals($this->underTest->getAPIEndpoint(), 'https://api.moltin.com/v2/');
    }

    public function testGetAPIEndpointWithURIReturnsCorrectURL()
    {
        $this->assertEquals($this->underTest->getAPIEndpoint('products/relationships/categories'), 'https://api.moltin.com/v2/products/relationships/categories');
    }

    public function testSetBaseURLUpdatesAPIEndpoint()
    {
        $customURL = 'https://api.yourcompany.com';
        $this->underTest->setBaseURL($customURL);
        $this->assertEquals($this->underTest->getAPIEndpoint(), 'https://api.yourcompany.com/v2/');
    }

}
