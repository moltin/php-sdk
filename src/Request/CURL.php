<?php

/**
 * This file is part of Moltin PHP-SDK, a PHP package which
 * provides convinient and rapid access to the API.
 *
 * Copyright (c) 2013 Moltin Ltd.
 * http://github.com/moltin/php-sdk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Jamie Holdroyd <jamie@molt.in>
 * @copyright 2013 Moltin Ltd.
 *
 * @version dev
 *
 * @link http://github.com/moltin/php-sdk
 */
namespace Moltin\SDK\Request;

class CURL implements \Moltin\SDK\RequestInterface
{
    public $url;
    public $method;
    public $code;
    public $time;
    public $header;

    protected $curl;
    protected $options = array();

    public function setup($url, $method, $post = array(), $token = null)
    {
        // Variables
        $headers = array();
        $this->curl = curl_init();
        $this->url = $url;
        $this->method = $method;
        $this->options = array(
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 40,
            CURLINFO_HEADER_OUT => true,
        );

        if ('POST' == $method) {
            $this->options[CURLOPT_POST] = true;
        }

        // Add post
        if (!empty($post)) {
            $post = $this->toFormattedPostData($post, $_FILES);
            $this->options[CURLOPT_POSTFIELDS] = $post;
        }

        // Add auth header
        if ($token !== null) {
            $headers[] = 'Authorization: Bearer '.$token;
        }

        // Add currency header
        if (isset($_SESSION['currency']) and $_SESSION['currency'] !== null) {
            $headers[] = 'X-Currency: '.$_SESSION['currency'];
        }

        // Add language header
        if (isset($_SESSION['language']) and $_SESSION['language'] !== null) {
            $headers[] = 'X-Language: '.$_SESSION['language'];
        }

        // Add session header
        $headers[] = 'X-Moltin-Session: '.session_id();

        // Set headers
        $this->options[CURLOPT_HTTPHEADER] = $headers;
    }

    /**
     * Recursive function that will generate an inline array to be send to the API
     *
     * @param  array  $value Array of keys/values to be processed
     * @param  string $key
     * @param  string $index Field key e.g. categories, orders
     * @return array  Array with all the resultant keys/values
     */
    protected function generateInlineArray($value, $key = '', $index = '') {
        if (is_array($value)) {
            $result = array();
            foreach($value as $k => $v) {
                $tmp = $this->generateInlineArray($v, $k, $index);
                if(isset($tmp['index']) && isset($tmp['value'])) {
                    // processing simple case
                    $result[$index . (!empty($key) ? '['.$key.']' : '') . '['.$tmp['index'].']'] = $tmp['value'];
                } else {
                    // use simple case to process complex case
                    $result = array_merge($result, $tmp);
                }
            }
            return $result;
        } else {
            // base case, no recursive call
            return array(
                'index' => $key,
                'value' => $value
            );
        }
    }

    public function make()
    {
        // Make request
        curl_setopt_array($this->curl, $this->options);
        $result = curl_exec($this->curl);
        $this->code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $this->time = curl_getinfo($this->curl, CURLINFO_TOTAL_TIME);
        $this->header = curl_getinfo($this->curl, CURLINFO_HEADER_OUT);

        return $result;
    }

    /**
     * Properly format an array of data, and optionally $files,
     * to send with the request.
     *
     * @param $post array
     * @param $files array
     * @return array
     */
    protected function toFormattedPostData(array $post, array $files = array())
    {
        // Merge in files
        foreach ($files as $key => $data) {
            if (!isset($post[$key]) and strlen($data['tmp_name']) > 0) {
                $post[$key] = new \CurlFile($data['tmp_name'], $data['type'], $data['name']);
            }
        }

        // Inline arrays
        foreach ($post as $key => $value) {
            if (is_array($value)) {
                $post = array_merge($post, $this->generateInlineArray($value, '', $key));
                unset($post[$key]);
            }
        }

        return $post;
    }
}
