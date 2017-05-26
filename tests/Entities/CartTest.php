<?php

namespace Moltin\SDK\Tests\Entities;

use Moltin;
use Moltin\Entities\Cart as Cart;
use Moltin\Response as Response;
use Mockery;

class CartTest extends \PHPUnit_Framework_TestCase
{
    private $underTest;
    private $client;
    private $storage;
    private $requestLibrary;

    public function setUp()
    {
        $this->client = Mockery::mock('Moltin\Client');
        $this->client->shouldReceive('getAPIEndpoint')
            ->andReturn('https://api.moltin.com')
            ->shouldReceive('getAuthEndpoint')
            ->andReturn('https://api.moltin.com/oauth/access_token')
            ->shouldReceive('getClientID')
            ->andReturn('123')
            ->shouldReceive('getClientSecret')
            ->andReturn('456')
            ->shouldReceive('getCurrencyCode')
            ->andReturn('CURRENCY_CODE')
            ->shouldReceive('getCookieCartName')
            ->andReturn('moltin_cart_cookie_reference')
            ->shouldReceive('getCookieLifetime')
            ->andReturn('+1 week');

        $this->storage = Mockery::mock('Moltin\Session');
        $sessonObject = new \stdClass();
        $sessonObject->access_token = '7893e06821bfbee0ea82afe2942dab734713cf5a';
        $sessonObject->expires = time() + 600;
        $this->storage->shouldReceive('getKey')
            ->with('authentication')
            ->andReturn($sessonObject);

        $this->storage->shouldReceive('getKey')
            ->with('cart_reference')
            ->andReturn('b31cd5e4c5cc8cf3d0f04271f5d2fcce');

        $cartResponseData = new \StdClass;
        $cartResponseData->id = '4c5df6bc-aa47-4481-b5b9-a2beb1e83916';
        $response = Mockery::mock('Moltin\Response');
        $response->shouldReceive('getStatusCode')
            ->andReturn(201)
            ->shouldReceive('data')
            ->andReturn($cartResponseData);

        $this->requestLibrary = Mockery::mock('Moltin\Request');
        $this->requestLibrary->shouldReceive('make')
            ->andReturn($this->requestLibrary)
            ->shouldReceive('setURL')
            ->andReturn($this->requestLibrary)
            ->shouldReceive('addHeaders')
            ->andReturn($this->requestLibrary)
            ->shouldReceive('setBody')
            ->andReturn($this->requestLibrary)
            ->shouldReceive('addHeader')
            ->andReturn($this->requestLibrary)
            ->shouldReceive('setMethod')
            ->andReturn($this->requestLibrary)
            ->shouldReceive('getResponse')
            ->andReturn($response)
            ->shouldReceive('setQueryStringParams')
            ->andReturn($response)
            ->shouldReceive('getRaw')
            ->andReturn(new \StdClass);

        $cart = new Cart('6c54b4a4-20ed-4378-9839-abf58dfa079e', $this->client, $this->requestLibrary, $this->storage);
        $this->underTest = $cart;
    }

    public function testCreateReferenceReturnsUnique()
    {
        $this->assertNotEquals($this->underTest->createReference(), $this->underTest->createReference());
    }

    public function testGetReferenceReturnsReference()
    {
        $this->assertEquals($this->underTest->getReference(), '6c54b4a4-20ed-4378-9839-abf58dfa079e');
    }

    public function testGetReferenceReturnsFromCookie()
    {
        $this->underTest->setReference(false);
        $_COOKIE['moltin_cart_cookie_reference'] = 'ecab0864-e648-450e-b5d1-b6ed83f5b7fc';
        $this->assertEquals($this->underTest->getReference($this->client), 'ecab0864-e648-450e-b5d1-b6ed83f5b7fc');
    }

    public function testGetReferenceSetsCookie()
    {
        $this->underTest->setReference(false);
        unset($_COOKIE['moltin_cart_cookie_reference']);
        $reference = $this->underTest->getReference($this->client);
        $this->assertNotEquals($reference, false);
    }

    public function testAddProductReturnsResponse()
    {
        $this->assertInstanceof(Response::class, $this->underTest->addProduct('2441493e-5e83-4d2d-afec-6a98f45d75c9'));
    }

    public function testGetItemsReturnsResponse()
    {
        $this->assertInstanceof(Response::class, $this->underTest->items());
    }

    public function testUpdateItemQuantityReturnsResponse()
    {
        $this->assertInstanceof(Response::class, $this->underTest->updateItemQuantity('940861cc-18e3-430b-9db6-e56ba6d8a0ef', 2));
    }

    public function testRemoveItemReturnsResponse()
    {
        $this->assertInstanceof(Response::class, $this->underTest->removeItem('1e2a40af-9aef-426a-8c33-921eb64a1754'));
    }

    public function testGetReferenceWithNoReferenceSetsReference()
    {
        $storage = Mockery::mock('Moltin\Session');
        $sessonObject = new \stdClass();
        $sessonObject->access_token = '7893e06821bfbee0ea82afe2942dab734713cf5a';
        $sessonObject->expires = time() + 600;
        $storage->shouldReceive('getKey')
            ->with('authentication')
            ->andReturn($sessonObject);
        $storage->shouldReceive('getKey')
            ->with('cart_reference')
            ->andReturn(false);
        $storage->shouldReceive('setKey');

        $response = Mockery::mock('Moltin\Response');

        $cart = new Cart(false, $this->client, $this->requestLibrary, $storage);

        $this->assertNotEmpty($cart->getReference());
    }

    public function testCheckoutMethodReturnsOrder()
    {
        $customer = [];
        $billing = [];
        $shipping = [];

        $cart = new Cart(false, $this->client, $this->requestLibrary, $this->storage);
        $this->assertInstanceOf(Moltin\Entities\Order::class, $cart->checkout($customer, $billing, $shipping));
    }

    /**
     * @expectedException Moltin\Exceptions\UnableToCheckoutException
     */
    public function testCheckoutMethodThrowsException()
    {

        $cartResponseData = new \StdClass;
        $cartResponseData->id = '4c5df6bc-aa47-4481-b5b9-a2beb1e83916';
        $response = Mockery::mock('Moltin\Response');
        $response->shouldReceive('getStatusCode')
            ->andReturn(400)
            ->shouldReceive('data')
            ->andReturn($cartResponseData);

        $requestLibrary = Mockery::mock('Moltin\Request');
        $requestLibrary->shouldReceive('make')
            ->andReturn($requestLibrary)
            ->shouldReceive('setURL')
            ->andReturn($requestLibrary)
            ->shouldReceive('addHeaders')
            ->andReturn($requestLibrary)
            ->shouldReceive('setBody')
            ->andReturn($requestLibrary)
            ->shouldReceive('addHeader')
            ->andReturn($requestLibrary)
            ->shouldReceive('setMethod')
            ->andReturn($requestLibrary)
            ->shouldReceive('getResponse')
            ->andReturn($response)
            ->shouldReceive('setQueryStringParams')
            ->andReturn($response)
            ->shouldReceive('getRaw')
            ->andReturn(new \StdClass);

        $cart = new Cart(false, $this->client, $requestLibrary, $this->storage);
        $cart->checkout([], [], []);
    }

}
