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
* @package moltin/php-sdk
* @author Jamie Holdroyd <jamie@molt.in>
* @copyright 2013 Moltin Ltd.
* @version dev
* @link http://github.com/moltin/php-sdk
*
*/

namespace Moltin\SDK;

use Moltin\SDK\Exception\InvalidRequestException as InvalidRequest;
use Moltin\SDK\Exception\InvalidResponseException as InvalidResponse;

class SDK
{
    // Paths
    public $version  = 'v1';
    public $url      = 'https://api.molt.in/';
    public $auth_url = 'http://auth.molt.in/';

    // Variables
    public $methods = array('GET', 'POST', 'PUT', 'DELETE');
    public $currency;
    public $language;
    public $store;
    public $request;

    // OAuth
    protected $token;
    protected $refresh;
    protected $expires;

    public function __construct(\Moltin\SDK\StorageInterface $store, \Moltin\SDK\RequestInterface $request, $args = array())
    {
        // Make global
        $this->store   = $store;
        $this->request = $request;

        // Setup args
        if ( isset($args['url']) and $args['url'] !== null ) { $this->url = $args['url']; }
        if ( isset($args['auth_url']) and $args['auth_url'] !== null ) { $this->auth_url = $args['auth_url']; }
        if ( isset($args['version']) and $args['version'] !== null ) { $this->version = $args['version']; }

        // Retrieve information
        $this->currency = $this->currency();
        $this->language = $this->language();
        $this->token    = $this->store->get('token');
        $this->refresh  = $this->store->get('refresh');
        $this->expires  = $this->store->get('expires');

        // Register facade alias
        $this->_registerFacades();
    }

    public function authenticate(\Moltin\SDK\AuthenticateInterface $auth, $args = array())
    {
        // Skip active auth or refresh current
        if ($this->expires > 0 and $this->expires > time()) {
            return true;
        } else if ($this->expires > 0 and $this->expires < time() and $this->refresh !== null) {
            return $this->refresh($args);
        }

        // Perform authentication
        $auth->authenticate($args, $this);

        // Store
        $this->_storeToken($auth);

        return ($this->token === null ? false : true);
    }

    public function refresh($args = array())
    {
        // Perform refresh
        $refresh = new Authenticate\Refresh();
        $refresh->authenticate($args, $this);

        // Store
        $this->_storeToken($refresh);

        return ($this->token === null ? false : true);
    }

    public function get($uri, $data = array())
    {
        $url = $this->url.$this->version.'/'.$uri;
        return $this->_request($url, 'GET', $data);
    }

    public function post($uri, $data = array())
    {
        $url = $this->url.$this->version.'/'.$uri;
        return $this->_request($url, 'POST', $data);
    }

    public function put($uri, $data = array())
    {
        $url = $this->url.$this->version.'/'.$uri;
        return $this->_request($url, 'PUT', $data);
    }

    public function delete($uri, $data = array())
    {
        $url = $this->url.$this->version.'/'.$uri;
        return $this->_request($url, 'DELETE', $data);
    }

    public function fields($type, $id = null, $wrap = false, $suffix = 'fields')
    {
        // Variables
        $fields = $this->get($type . ($id !== null ? '/' . $id : '') . '/' . $suffix);
        $flows = new Flows($fields['result'], $wrap);

        // Build and return form
        return $flows->build($fields);
    }

    public function identifier($reset = false, $identifier = null)
    {
        if ($reset) {
            setcookie('mcart', null, -1, '/');
            unset($_COOKIE['mcart']);
        }

        if (isset($_COOKIE['mcart'])) {
            return $_COOKIE['mcart'];
        }

        if(! $identifier) {
            $identifier = md5(uniqid());
        }

        setcookie('mcart', $identifier, strtotime("+30 day"), '/');

        return $identifier;
    }

    public function currency($code = null)
    {
        return $this->language($code, 'currency');
    }

    public function language($code = null, $key = 'language')
    {
        // Clear it
        if ( $code === false ) { unset($_SESSION[$key]); $this->$key = null; return true; }

        // Get it
        if ( $code === null and isset($_SESSION[$key]) ) { return $_SESSION[$key]; }

        // Set it
        if ( $code !== null ) { $this->$key = $code; $_SESSION[$key] = $code; return $code; }

        return false;
    }

    protected function _storeToken(\Moltin\SDK\AuthenticateInterface $auth)
    {
        // Get keys
        $this->token   = $auth->get('token');
        $this->refresh = $auth->get('refresh');
        $this->expires = $auth->get('expires');

        // Store them
        $this->store->insertUpdate('token',   $this->token);
        $this->store->insertUpdate('refresh', $this->refresh);
        $this->store->insertUpdate('expires', $this->expires);
    }

    protected function _request($url, $method, $data)
    {
        // Check type
        if ( ! in_array($method, $this->methods)) {
            throw new InvalidRequest('Invalid request type (' . $method . ')');
        }

        // Check token
        if ($this->token === null) {
            throw new InvalidRequest('You haven\'t authenticated yet');
        }

        // Check token expiration
        if ($this->expires !== null and time() > $this->expires) {
            throw new InvalidRequest('Your current OAuth session has expired');
        }

        // Append URL
        if ($method == 'GET' and ! empty($data)) {
            $url .= '?' . http_build_query($data);
            $data = array();
        } else if ($method == 'PUT') {
            $url .= (strpos($url, '?') !== false ? '&' : '?').'_method='.$method;
            $method = 'POST';
        }

        // Start request
        $this->request->setup($url, $method, $data, $this->token);

        // Make request
        $result = $this->request->make();

        // Check response
        $result = json_decode($result, true);

        // Check JSON for error
        if (isset($result['status']) and ! $result['status']) {

            $error = "Unknown error";

            // Catch multiple errors
            if(isset($result['errors'])) {
                $error = $this->_implodeErrors($result['errors']);
            }

            // Catch the singular type second
            if(isset($result['error'])) {
                $error = $this->_implodeErrors($result['error']);
            }

            throw new InvalidResponse($error);
        }

        // Return response
        return $result;
    }

    /**
     * Squash errors down from arrays if needed
     *
     * @param $errors mixed The array or string of errors
     * @return string
     */
    protected function _implodeErrors($errors) {
        if(is_array($errors)) {
            $error = '';
            foreach ( $errors as $e ) {
                if ( is_array($e) ) {
                    $error .= implode("\n", $e);
                } else {
                    $error .= "\n".$e;
                }
            }
        }else{
            $error = $errors;
        }

        return $error;
    }

    protected function _registerFacades()
    {
        foreach (glob(__DIR__.'/Facade/*') as $facade) {
            $facade = strstr(basename($facade), '.php', true);
            if ( class_exists($facade) ) { continue; }
            class_alias('\\Moltin\\SDK\\Facade\\'.$facade, $facade);
            $facade::init($this);
        }
    }

}
