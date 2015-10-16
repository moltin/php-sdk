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

    public function setup($url, $method, $post = array(), $token = null)
    {
        // Variables
        $headers = [];
        $this->curl = curl_init();
        $this->url = $url;
        $this->method = $method;

        // Add request settings
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 40,
            CURLINFO_HEADER_OUT => true,
        ));

        // Add post
        if (!empty($post)) {
            $post = $this->toFormattedPostData($post, $_FILES);

            // Assign to curl
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
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
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
    }

    public function make()
    {
        // Make request
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
            // $key => order
            // $value => array with all the parents and children
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    // $k => id parent or children
                    // $v => parent or children information
                    if (isset($v) && !empty($v)) {
                        if (empty($v['parent'])) {
                            $post[$key.'['.$k.'][order]'] = $v['order'];
                        } elseif (!empty($v['parent'])) {
                            $post[$key.'['.$k.'][order]'] = $v['order'];
                            $post[$key.'['.$k.'][parent]'] = $v['parent'];
                        }
                    }
                }
                unset($post[$key]);
            }
        }

        return $post;
    }
}
