<?php

namespace Moltin\SDK\Tests;

use Moltin\SDK\Request\CURL;

class CURLTest extends \PHPUnit_Framework_TestCase
{
    public function test_inline_arrays_to_formatted_post_data() {
        $method = self::getMethod('toFormattedPostData');
        $curl = $this->newCurlInstance();
        $resultOrderFormattedPostData = $method->invokeArgs($curl, [$this->getOrderInlineArrayPostData()]);
        $resultCategoriesFormattedPostData = $method->invokeArgs($curl, [$this->getCategoriesInlineArrayPostData()]);

        $orderFormattedPostData = $this->getOrderInlineArrayFormattedPostData();
        $categoriesFormattedPostData = $this->getCategoriesInlineArrayFormattedPostData();

        $this->assertEquals($orderFormattedPostData, $resultOrderFormattedPostData);
        $this->assertEquals($resultCategoriesFormattedPostData, $categoriesFormattedPostData);
    }

    protected static function getMethod($name) {
        $class = new \ReflectionClass('Moltin\SDK\Request\CURL');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * Return an order inline array simulating what will be a post request for order categories
     *
     * @return array
     */
    private function getOrderInlineArrayPostData()
    {
        return [
            'order' => [
                619  => [
                    'order'  => 1,
                    'parent' => 0,
                ],
                653  => [
                    'order'  => 1,
                    'parent' => '619',
                ],
                650  => [
                    'order'  => 2,
                    'parent' => '619',
                ],
                652  => [
                    'order'  => 3,
                    'parent' => '619',
                ],
                651  => [
                    'order'  => 4,
                    'parent' => '619',
                ],
                646  => [
                    'order'  => 5,
                    'parent' => '619',
                ],
                648  => [
                    'order'  => 6,
                    'parent' => '619',
                ],
                647  => [
                    'order'  => 7,
                    'parent' => '619',
                ],
                649  => [
                    'order'  => 8,
                    'parent' => '619',
                ],
                624  => [
                    'order'  => 2,
                    'parent' => 0,
                ],
                703  => [
                    'order'  => 1,
                    'parent' => '624',
                ],
                701  => [
                    'order'  => 2,
                    'parent' => '624',
                ],
                704  => [
                    'order'  => 3,
                    'parent' => '624',
                ],
                705  => [
                    'order'  => 4,
                    'parent' => '624',
                ],
                699  => [
                    'order'  => 5,
                    'parent' => '624',
                ],
                698  => [
                    'order'  => 6,
                    'parent' => '624',
                ],
                700  => [
                    'order'  => 7,
                    'parent' => '624',
                ],
                702  => [
                    'order'  => 8,
                    'parent' => '624',
                ]
            ]
        ];
    }

    /**
     * Return an order inline array already formatted to be send to the API
     *
     * @return array
     */
    private function getOrderInlineArrayFormattedPostData()
    {
        return [
            'order[619][order]' => 1,
            'order[619][parent]' => 0,
            'order[651][order]' => 4,
            'order[651][parent]' => '619',
            'order[646][order]' => 5,
            'order[646][parent]' => '619',
            'order[648][order]' => 6,
            'order[648][parent]' => '619',
            'order[652][order]' => 3,
            'order[652][parent]' => '619',
            'order[647][order]' => 7,
            'order[647][parent]' => '619',
            'order[650][order]' => 2,
            'order[650][parent]' => '619',
            'order[649][order]' => 8,
            'order[649][parent]' => '619',
            'order[653][order]' => 1,
            'order[653][parent]' => '619',
            'order[624][order]' => 2,
            'order[624][parent]' => 0,
            'order[703][order]' => 1,
            'order[703][parent]' => '624',
            'order[701][order]' => 2,
            'order[701][parent]' => '624',
            'order[704][order]' => 3,
            'order[704][parent]' => '624',
            'order[705][order]' => 4,
            'order[705][parent]' => '624',
            'order[699][order]' => 5,
            'order[699][parent]' => '624',
            'order[698][order]' => 6,
            'order[698][parent]' => '624',
            'order[700][order]' => 7,
            'order[700][parent]' => '624',
            'order[702][order]' => 8,
            'order[702][parent]' => '624',
        ];
    }

    /**
     * Return an categories inline array already formatted to be send to the API
     *
     * @return array
     */
    private function getCategoriesInlineArrayPostData()
    {
        return [
            'category' => [
                0 => '653',
                1 => '619',
                2 => '703',
            ]
        ];
    }

    /**
     * Return an categories inline array simulating what will be a post request for selected categories
     *
     * @return array
     */
    private function getCategoriesInlineArrayFormattedPostData()
    {
        return [
            'category[0]' => '653',
            'category[1]' => '619',
            'category[2]' => '703',
        ];
    }

    /**
     * Return a CURL class instance
     *
     * @return CURL
     */
    private function newCurlInstance()
    {
        return new CURL();
    }
}