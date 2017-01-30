<?php

namespace Moltin\SDK\Tests\Entities;

use Moltin;
use Moltin\Entities\Order as Order;
use Moltin\Response as Response;
use Mockery;

class OrderTest extends \PHPUnit_Framework_TestCase
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
            ->andReturn('CURRENCY_CODE');

        $this->storage = Mockery::mock('Moltin\Session');
        $sessonObject = new \stdClass();
        $sessonObject->access_token = '7893e06821bfbee0ea82afe2942dab734713cf5a';
        $sessonObject->expires = time() + 600;
        $this->storage->shouldReceive('getKey')
            ->with('authentication')
            ->andReturn($sessonObject);

        $response = Mockery::mock('Moltin\Response');

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

        $order = new Order($this->client, $this->requestLibrary, $this->storage);
        $this->underTest = $order;
    }

    public function testCanSetAndGetID()
    {
        $this->underTest->setID('63c97277-334f-4bcb-b0a7-3ff9f65abfbd');
        $this->assertEquals($this->underTest->getID(), '63c97277-334f-4bcb-b0a7-3ff9f65abfbd');
    }

    public function testCanSetAndGetData()
    {
        $this->underTest->setData(['id' => '805801a9-4c57-4c58-b3dd-a7cc2a4e4ec5']);
        $this->assertEquals($this->underTest->getData(), ['id' => '805801a9-4c57-4c58-b3dd-a7cc2a4e4ec5']);
    }

    public function testCanSetAndGetCart()
    {
        $this->underTest->setCart(['id' => '41fecb2b-57ab-4dbe-a54f-13141fa6b484']);
        $this->assertEquals($this->underTest->getCart(), ['id' => '41fecb2b-57ab-4dbe-a54f-13141fa6b484']);
    }

    public function testCanMakeMergePaymentData()
    {
        $this->assertEquals($this->underTest->mergePayData('stripe', 'purchase', ['number' => '4242424242424242']), ['number' => '4242424242424242', 'gateway' => 'stripe', 'method' => 'purchase']);
    }

    public function testCanMakePayCall()
    {
        $this->assertInstanceof(Response::class, $this->underTest->pay('stripe', 'purchase', ['number' => '4242424242424242']));
    }
}
