<?php

namespace Moltin\SDK\Tests;

use Moltin;

class FilterTest extends \PHPUnit_Framework_TestCase
{

    private $underTest;

    public function setUp()
    {
        $this->underTest = new Moltin\Filter();
    }

    /**
     *  @dataProvider constructProvider
     */
    public function testConstruct($params, $expected)
    {
        $filter = new Moltin\Filter($params);
        $this->assertEquals($filter->getFilters(), $expected);
    }

    public function constructProvider()
    {
        return [
            [[], []],
            [['eq' => ['status' => 'live']], ['eq' => ['status' => 'live']]],
            [['eq' => ['status' => 'live'], 'uh' => ['description' => '*hg*']], ['eq' => ['status' => 'live']]]
        ];
    }

    /**
     *  @dataProvider addFilterProvider
     */
    public function testAddFilter($operator, $attribute, $value, $expected)
    {
        $filter = new Moltin\Filter();
        $filter->addFilter($operator, $attribute, $value);
        $this->assertEquals((string) $filter, $expected);
    }

    public function addFilterProvider()
    {
        return [
            ['eq', '', '', ''],
            ['eq', 'status', '', 'eq(status,)'],
            ['eq', 'status', 'live', 'eq(status,live)']
        ];
    }

    public function testEmptyRulesReturnsEmptyArray()
    {
        $filter = new Moltin\Filter();
    }

    public function testCanRemoveFilter()
    {
        $filter = new Moltin\Filter();
        $filter->addFilter('eq', 'status', 'live');
        $filter->removeFilter('eq', 'status');
        $this->assertEquals((string) $filter,'');
    }

    /**
     *  @dataProvider multiFilterProvider
     */
    public function testMultipleFilters($filters, $expected)
    {
        $filter = new Moltin\Filter($filters);
        $this->assertEquals((string) $filter, $expected);
    }

    public function multiFilterProvider()
    {
        return [
            [[], ''],
            [['eq' => ['status' => 'live']], 'eq(status,live)'],
            [['eq' => ['status' => 'live', 'manage_stock' => 'true']], 'eq(status,live):eq(manage_stock,true)'],
            [['eq' => ['status' => 'live', 'manage_stock' => 'true'], 'ne' => ['stock' => 5]], 'eq(status,live):eq(manage_stock,true):ne(stock,5)'],
        ];
    }

}
