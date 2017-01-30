<?php

namespace Moltin\SDK\Tests;

use Moltin;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    private $rawResponse;
    private $underTest;

    public function setUp()
    {
        $this->rawResponse = '{"data":[{"type":"product","id":"80087cb1-1197-4942-83e2-40f3da39d3a1","name":"My Great Product","slug":"my-great-product","sku":"MGP_001","manage_stock":true,"description":"","price":[{"amount":5891,"currency":"USD","includes_tax":true},{"amount":7150,"currency":"GBP","includes_tax":true}],"status":"live","commodity_type":"physical","meta":{"display_price":{"with_tax":{"amount":7150,"currency":"GBP","formatted":"\u00a371.5"},"without_tax":{"amount":7150,"currency":"GBP","formatted":"\u00a371.5"}},"stock":{"level":0,"availability":"out-stock"}},"relationships":{}}],"included":{"categories":[{"id":"02a74d61-9840-4dce-ac40-b156d2a71cf2","type":"category","status":"draft","name":"My First Category","slug":"my-first-category-89be7a5897521fc0d89ec69bd0bcdbcc07052414","description":"","relationships":{"products":[{"type":"product","id":"ac964ac0-3740-458d-933a-8647041032a3"}]}},{"id":"49398013-bf43-42f8-bfda-fdf6228d0ea8","type":"category","status":"draft","name":"My Second Category","slug":"my-second-category-89be7a5897521fc0d89ec69bd0bcdbcc07052414","description":"","relationships":{"products":[{"type":"product","id":"ac964ac0-3740-458d-933a-8647041032a3"}]}}]},"links":{"current":"https:\/\/api.moltin.com\/v2\/products\/80087cb1-1197-4942-83e2-40f3da39d3a1","last":null},"meta":{"counts":{"matching_resource_count":1}},"errors":[]}';
        $this->underTest = new Moltin\Response();
        $this->underTest->setRaw(json_decode($this->rawResponse))->parse();
    }

    public function testCanGetRaw()
    {
        $this->assertEquals(json_encode($this->underTest->getRaw()), $this->rawResponse);
    }

    public function testCanGetData()
    {
        $this->assertEquals(count($this->underTest->data()), 1);
    }

    public function testCanGetIncluded()
    {
        $this->assertEquals(count($this->underTest->included()->categories), 2);
    }

    public function testCanGetErrors()
    {
        $this->assertEquals(count($this->underTest->errors()), 0);
    }

    public function testCanGetMeta()
    {
        $meta = new \StdClass;
        $meta->counts = new \StdClass;
        $meta->counts->matching_resource_count = 1;
        $this->assertEquals($this->underTest->meta(), $meta);
    }

    public function testCanGetLinks()
    {
        $this->assertEquals(count($this->underTest->links()), 1);
    }

    public function testCanSetAndGetRequestID()
    {
        $id = 'd009b51016a88ab7ff1795ef8ea085c537814ba5';
        $this->underTest->setRequestID($id);
        $this->assertEquals($this->underTest->getRequestID(), $id);
    }

    public function testCanSetAndGetStatusCode()
    {
        $code = 201;
        $this->underTest->setStatusCode($code);
        $this->assertEquals($this->underTest->getStatusCode(), $code);
    }

    public function testCanSetAndGetExecutionTime()
    {
        $time = 0.21640;
        $this->underTest->setExecutionTime($time);
        $this->assertEquals($this->underTest->getExecutionTime(), $time);
    }
}
