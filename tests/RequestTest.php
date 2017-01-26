<?php

namespace Moltin\SDK\Tests;

use Moltin;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    private $underTest;

    public function setUp()
    {
        $client = \Mockery::mock(GuzzleHttp\Client::class);
        $result = \Mockery::mock(GuzzleHttp\Psr7\Response::class);
        $result->shouldReceive('getStatusCode')
            ->andReturn(200)
            ->shouldReceive('getHeader')
            ->with('X-Moltin-Request-Id')
            ->andReturn('abc-123')
            ->shouldReceive('getBody')
            ->andReturn('{data:[responded: true]}');
        $client->shouldReceive('request')
            ->andReturn($result);
        $this->underTest = new Moltin\Request($client);
    }

    public function testCanGetAndSetMethod()
    {
        $this->underTest->setMethod('pUt');
        $this->assertEquals('PUT', $this->underTest->getMethod());
    }

    /**
     * @expectedException Moltin\Exceptions\InvalidRequestMethod
     */
    public function testSetMethodToInvlaidValueThrowsException()
    {
        $this->underTest->setMethod('replace');
    }

    public function testCanGetHeaders()
    {
        $expected = [];
        $this->assertEquals([], $this->underTest->getHeaders());
    }

    public function testCanAddASingleHeader()
    {
        $this->assertEquals(['MY-HEADER' => 'MY-VALUE'], $this->underTest->addHeader('MY-HEADER', 'MY-VALUE')->getHeaders());
    }

    public function testCanAddMultipleHeaders()
    {
        $this->underTest->addHeaders(['MY-FIRST-HEADER' => 'MY-FIRST-VALUE', 'MY-SECOND-HEADER' => 'MY-SECOND-VALUE']);
        $this->assertEquals(['MY-FIRST-HEADER' => 'MY-FIRST-VALUE', 'MY-SECOND-HEADER' => 'MY-SECOND-VALUE'], $this->underTest->getHeaders());
    }

    public function testCanClearHeaders()
    {
        $this->underTest->addHeader('MY-HEADER', 'MY-VALUE')->getHeaders();
        $this->underTest->clearHeaders();
        $this->assertEquals([], $this->underTest->getHeaders());
    }

    public function testCanGetAndSetBody()
    {
        $this->underTest->setBody(['my-body-data']);
        $this->assertEquals(['my-body-data'], $this->underTest->getBody());
    }

    public function testCanGetAndSetURL()
    {
        $this->underTest->setURL('https://mydomain.com');
        $this->assertEquals('https://mydomain.com', $this->underTest->getURL());
    }

    public function testBodyKeyCanReturnJSON()
    {
        $this->underTest->addHeader('Content-Type', 'application/json');
        $this->assertEquals('json', $this->underTest->getBodyKey());
    }

    public function testBodyKeyCanReturnFormParams()
    {
        $this->underTest->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->assertEquals('form_params', $this->underTest->getBodyKey());
    }

    public function testBodyKeyCanReturnMultipart()
    {
        $this->underTest->addHeader('Content-Type', 'multipart/form-data');
        $this->assertEquals('body', $this->underTest->getBodyKey());
    }

    public function testMultipartPayloadReturnsArray()
    {
        $payload = [
            'body' => [
                [
                    'name' => 'field',
                    'contents' => 'value'
                ]
            ]
        ];

        $payload = $this->underTest->prepareMultipartPayload($payload);

        $this->assertContains('multipart/form-data; boundary=', $payload['headers']['Content-Type']);
        $this->assertInstanceOf(\GuzzleHttp\Psr7\MultipartStream::class, $payload['body']);
    }

    public function testgetPayloiadCallsMultipart()
    {
        $this->underTest->addHeader('Content-Type', 'multipart/form-data');
        $this->underTest->setBody([
                [
                    'name' => 'field',
                    'contents' => 'value'
                ]
        ]);

        $payload = $this->underTest->getPayload();
        $this->assertContains('multipart/form-data; boundary=', $payload['headers']['Content-Type']);
        $this->assertInstanceOf(\GuzzleHttp\Psr7\MultipartStream::class, $payload['body']);
    }

    /**
     * @expectedException Moltin\Exceptions\InvalidContentType
     */
    public function testBodyKeyThrowsExceptionWithInvalidContentType()
    {
        $this->underTest->addHeader('Content-Type', 'nope/not');
        $this->underTest->getBodyKey();
    }

    /**
     * @expectedException Moltin\Exceptions\InvalidContentType
     */
    public function testBodyKeyThrowsExceptionWithNoContentType()
    {
        $this->underTest->getBodyKey();
    }

    public function testCanGetAndSetQueryParams()
    {
        $this->underTest->setQueryStringParams(['page' => ['limit' => 10]]);
        $this->assertEquals(['page' => ['limit' => 10]], $this->underTest->getQueryStringParams());
    }

    public function testCanGetPayload()
    {
        $expects = [
            'json' => [
                'data' => []
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'My-Header' => 'my-value'
            ],
            'query' => [
                'page' => [
                    'limit' => 10
                ]
            ]
        ];

        $this->underTest->setQueryStringParams(['page' => ['limit' => 10]]);
        $this->underTest->setBody(['data' => []]);
        $this->underTest->addHeader('Content-Type', 'application/json');
        $this->underTest->addHeader('My-Header', 'my-value');

        $this->assertEquals($expects, $this->underTest->getPayload());
    }

    public function testMakeReturnsResponse()
    {
        $this->assertInstanceOf(\Moltin\Response::class, $this->underTest->make()->getResponse());
    }

}
