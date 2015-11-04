<?php

namespace Moltin\SDK\Tests\Request;

use Moltin\SDK\Request\CURL;

class CURLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test toFormattedPostData
     *
     * @param array $data
     * @param array $expected
     *
     * @dataProvider getOrderInlineArrayPostDataProvider
     * @dataProvider getCategoriesInlineArrayPostDataProvider
     */
    public function test_inline_arrays_to_formatted_post_data($data, $expected) {
        $method = self::getMethod('toFormattedPostData');
        $curl = $this->newCurlInstance();
        $result = $method->invokeArgs($curl, array($data));

        $this->assertEquals($expected, $result);
    }

    public function test_that_empty_post_data_creates_post_request()
    {
        $request = $this->newCurlInstance();
        $request->setup('/checkout/payment/authorize', 'POST');
        $options = \PHPUnit_Framework_Assert::readAttribute($request, 'options');
        $this->assertArrayHasKey(CURLOPT_POST, $options);
        $this->assertEquals(true, $options[CURLOPT_POST]);

        $request = $this->newCurlInstance();
        $post = array('foo' => 'bar');
        $request->setup('/checkout/payment/authorize', 'POST', $post);
        $options = \PHPUnit_Framework_Assert::readAttribute($request, 'options');
        $this->assertArrayHasKey(CURLOPT_POST, $options);
        $this->assertArrayHasKey(CURLOPT_POSTFIELDS, $options);
        $this->assertEquals(true, $options[CURLOPT_POST]);
        $this->assertEquals($post, $options[CURLOPT_POSTFIELDS]);
    }

    public function getOrderInlineArrayPostDataProvider()
    {
        $dataOrder = array(
            'order' => array(
                619  => array('order' => 1, 'parent' => 0),
                653  => array('order' => 1, 'parent' => '619'),
                650  => array('order' => 2, 'parent' => '619'),
                652  => array('order' => 3, 'parent' => '619'),
                624  => array('order' => 2, 'parent' => 0),
                703  => array('order' => 1, 'parent' => '624'),
                701  => array('order' => 2, 'parent' => '624'),
                704  => array('order' => 3, 'parent' => '624')
            )
        );

        $expected = array(
            'order[619][order]' => 1,
            'order[619][parent]' => 0,
            'order[653][order]' => 1 ,
            'order[653][parent]' => '619',
            'order[650][order]' => 2,
            'order[650][parent]' => '619',
            'order[652][order]' => 3,
            'order[652][parent]' => '619',
            'order[624][order]' => 2,
            'order[624][parent]' => 0,
            'order[703][order]' => 1,
            'order[703][parent]' => '624',
            'order[701][order]' => 2,
            'order[701][parent]' => '624',
            'order[704][order]' => 3,
            'order[704][parent]' => '624'
        );

        return array(
            array(
                $dataOrder,
                $expected
            )
        );
    }

    public function getCategoriesInlineArrayPostDataProvider()
    {
        $dataCategory = array(
            'category' => array(
                0 => '653',
                1 => '619',
                2 => '703',
            )
        );

        $expected = array(
            'category[0]' => '653',
            'category[1]' => '619',
            'category[2]' => '703',
        );

        return array(
            array(
                $dataCategory,
                $expected
            )
        );
    }

    /**
     * Get a protected method through reflection
     *
     * @param $name
     * @return \ReflectionMethod
     */
    protected function getMethod($name) {
        $class = new \ReflectionClass('Moltin\SDK\Request\CURL');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
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
