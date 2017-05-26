<?php

namespace Moltin\SDK\Tests\Resources;

use Moltin;
use Moltin\Response;
use Mockery;

class FilesTest extends \PHPUnit_Framework_TestCase
{

    private $underTest;

    public function setUp()
    {
        $this->client = Mockery::mock('Moltin\Client');
        $this->underTest = new Moltin\Resources\Files($this->client);
    }

    /**
     * @expectedException Moltin\Exceptions\FileNotFoundException
     */
    public function testGetFileLocationThrowsExceptionWhenNoFileExists()
    {
        $this->underTest->getFileLocation('/does/not/exist');
    }

    public function testGetFileLocationReturnsWhenFileExists()
    {
        $this->assertEquals('./README.md', $this->underTest->getFileLocation('./README.md'));
    }

    public function testGetFileLocationReturnsWhenURLIsPassed()
    {
        $this->assertContains('/tmp/moltinfile_', $this->underTest->getFileLocation('https://placeholdit.imgix.net/~text?&w=350&h=150'));
    }

    public function testParseMulitpartDataReturnsEmptyArrayWithNoData()
    {
        $this->assertEquals([], $this->underTest->parseMulitpartData([]));
    }

    public function testParseMulitpartDataReturnsArrayWithData()
    {
        $this->assertEquals([
            [
                'name' => 'public',
                'contents' => 'true'
            ]
        ], $this->underTest->parseMulitpartData([
            'public' => 'true'
        ]));
    }

    public function testParseMulitpartDataExcludesFileInData()
    {
        $this->assertEquals([
            [
                'name' => 'public',
                'contents' => 'true'
            ]
        ], $this->underTest->parseMulitpartData([
            'public' => 'true',
            'file' => 'path/to/file'
        ]));
    }

}
