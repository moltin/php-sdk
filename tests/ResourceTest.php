<?php

namespace Moltin\SDK\Tests;

use Moltin;
use Moltin\Response;
use Mockery;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $storage;
    private $requestLibrary;
    private $underTest;

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

        $this->underTest = new Moltin\Resources\Products($this->client, $this->requestLibrary, $this->storage);
    }

    public function testSortMethodUpdatesSort()
    {
        $this->underTest->sort('-name');
        $this->assertEquals($this->underTest->getSort(), '-name');
    }

    public function testSortAsFalseMethodUpdatesSort()
    {
        $this->underTest->sort('-name');
        $this->underTest->sort(false);
        $this->assertEquals($this->underTest->getSort(), false);
    }

    public function testLimitMethodUpdatesLimit()
    {
        $this->underTest->limit(5);
        $this->assertEquals($this->underTest->getLimit(), 5);
    }

    public function testOffsetMethodUpdatesOffset()
    {
        $this->underTest->offset(20);
        $this->assertEquals($this->underTest->getOffset(), 20);
    }

    public function testOffsetMethodToFalseUpdatesOffset()
    {
        $this->underTest->offset(false);
        $this->assertEquals($this->underTest->getOffset(), false);
    }

    public function testGetStorageReturnsStorage()
    {
        $this->assertInstanceof(Moltin\Interfaces\Storage::class, $this->underTest->getStorage());
    }

    public function testGetFilterReturnsFilter()
    {
        $this->underTest->filter(['eq' => ['status', 'live']]);
        $this->assertInstanceof(Moltin\Filter::class, $this->underTest->getFilter());
    }

    public function testGetClientReturnsClient()
    {
        $this->assertInstanceof(Moltin\Client::class, $this->underTest->getClient());
    }

    public function testGetRelationshipTypeReturnsValueWithValidType()
    {
        $this->assertEquals($this->underTest->getRelationshipType('categories'), 'category');
    }

    public function testGetRelationshipTypeReturnsFalseWithInvalidType()
    {
        $this->assertEquals($this->underTest->getRelationshipType('doesntexist'), false);
    }

    public function testBuildRelationshipDataReturnsValidArray()
    {
        $expected = [
            ['type' => 'category', 'id' => 'fe743255-b387-4a37-a712-6e341e81a6ab'],
            ['type' => 'category', 'id' => '838ff042-7d4b-4b4b-8d6c-443e7368e73a']
        ];

        $this->assertEquals($this->underTest->buildRelationshipData('category', ['fe743255-b387-4a37-a712-6e341e81a6ab', '838ff042-7d4b-4b4b-8d6c-443e7368e73a']), $expected);
    }

    public function testBuildRelationshipDataWithStringReturnsValidSingleArray()
    {
        $expected = [
            'type' => 'category', 'id' => 'fe743255-b387-4a37-a712-6e341e81a6ab'
        ];

        $this->assertEquals($this->underTest->buildRelationshipData('category', 'fe743255-b387-4a37-a712-6e341e81a6ab'), $expected);
    }
    public function testBuildRelationshipDataWithNullReturnsNull()
    {
        $this->assertEquals($this->underTest->buildRelationshipData('category', null), null);
    }

    public function testBuildRelationshipEmptyArrayReturnsNull()
    {
        $this->assertEquals($this->underTest->buildRelationshipData('category', []), null);
    }

    public function testCanGetAttributes()
    {
        $this->assertInstanceof(Response::class, $this->underTest->attributes());
    }

    public function testCanMakeListRequest()
    {
        $this->assertInstanceof(Response::class, $this->underTest->all());
    }

    public function testCanMakeGetByIDRequest()
    {
        $id = 'c9b96b2f-574d-43f7-be53-3737959ddbb1';
        $this->assertInstanceof(Response::class, $this->underTest->get($id));
    }

    public function testCanMakeDeleteRequest()
    {
        $id = 'c9b96b2f-574d-43f7-be53-3737959ddbb1';
        $this->assertInstanceof(Response::class, $this->underTest->delete($id));
    }

    public function testCanMakeUpdateRequest()
    {
        $id = 'c9b96b2f-574d-43f7-be53-3737959ddbb1';
        $this->assertInstanceof(Response::class, $this->underTest->update($id, []));
    }

    public function testCanMakeCreateRequest()
    {
        $id = 'c9b96b2f-574d-43f7-be53-3737959ddbb1';
        $this->assertInstanceof(Response::class, $this->underTest->create([]));
    }

    public function testCanCreateRelationships()
    {
        $this->assertInstanceof(Response::class, $this->underTest->createRelationships('c9b96b2f-574d-43f7-be53-3737959ddbb1', 'categories', []));
    }

    public function testCanDeleteRelationships()
    {
        $this->assertInstanceof(Response::class, $this->underTest->deleteRelationships('c9b96b2f-574d-43f7-be53-3737959ddbb1', 'categories', []));
    }

    public function testCanUpdateRelationships()
    {
        $this->assertInstanceof(Response::class, $this->underTest->updateRelationships('c9b96b2f-574d-43f7-be53-3737959ddbb1', 'categories', []));
    }

    /**
     * @expectedException Moltin\Exceptions\InvalidRelationshipTypeException
     */
    public function testRelationshipCallWithInvalidTypeThrowsException()
    {
        $this->underTest->updateRelationships('c9b96b2f-574d-43f7-be53-3737959ddbb1', 'notreal', []);
    }

    public function testGetAccessTokenMakesAuthenticationCall()
    {
        $atResponse = new \stdClass;
        $atResponse->access_token = 'ef6206afa0a8a95d342c10b9eadb3082e19c8021';
        $response = Mockery::mock('Moltin\Response');
        $response->shouldReceive('getRaw')
            ->andReturn($atResponse);

        $this->storage = Mockery::mock('Moltin\Session');
        $this->storage->shouldReceive('getKey')
            ->with('authentication')
            ->andReturn(false)
            ->shouldReceive('setKey');

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
            ->shouldReceive('getRaw')
            ->andReturn(new \StdClass);

        $test = new Moltin\Resources\Products($this->client, $requestLibrary, $this->storage);

        $this->assertEquals('ef6206afa0a8a95d342c10b9eadb3082e19c8021', $test->getAccessToken());
    }

    /**
     * @expectedException Moltin\Exceptions\AuthenticationException
     */
    public function testGetAccessTokenWhichIsForbiddenThrowsException()
    {
        $response = Mockery::mock('Moltin\Response');
        $response->shouldReceive('getRaw')
            ->andReturn(false);

        $this->storage = Mockery::mock('Moltin\Session');
        $this->storage->shouldReceive('getKey')
            ->with('authentication')
            ->andReturn(false)
            ->shouldReceive('setKey');

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
            ->shouldReceive('getRaw')
            ->andReturn(new \StdClass);

        $test = new Moltin\Resources\Products($this->client, $requestLibrary, $this->storage);
        $test->makeAuthenticationCall();
    }

    public function testBuildQueryStringParams()
    {
        $this->underTest->with(['categories'])->limit(5)->offset(3)->sort('name')->filter(['eq' => ['stock' => 0, 'status' => 'draft']]);
        $this->assertEquals(
            ['page' => ['limit' => 5, 'offset' => 3], 'sort' => 'name', 'include' => 'categories', 'filter' => 'eq(stock,0):eq(status,draft)'],
            $this->underTest->buildQueryStringParams()
        );
    }

    public function testCanAddRequestHeader()
    {
        $this->assertEquals(['X-MOLTIN-CURRENCY' => 'CURRENCY_CODE'], $this->underTest->addRequestHeaders([]));
    }
}
