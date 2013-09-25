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

    // Test Paths
    public $version  = 'beta';
    public $url      = 'http://api.dev.molt.in/';
    public $auth_url = 'http://auth.dev.molt.in/';

    // Live Paths
    // public $version  = 'beta';
    // public $url      = 'http://api.molt.in/';
    // public $auth_url = 'http://auth.molt.in/';

    // Variables
    public $methods = array('GET', 'POST', 'PUT', 'DELETE');
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
        if ( isset($args['version']) ) { $this->version = $args['version']; }

        // Retrieve information
        $this->token   = $this->store->get('token');
        $this->refresh = $this->store->get('refresh');
        $this->expires = $this->store->get('expires');
    }

    public function authenticate(\Moltin\SDK\AuthenticateInterface $auth, $args = array())
    {
        // Perform authentication
        $auth->authenticate($args, $this);

        // Store
        $this->_storeToken($auth);

        return ( $this->token === null ? false : true );
    }

    public function refresh(\Moltin\SDK\AuthenticateInterface $auth, $args = array())
    {
        // Perform refresh
        $auth->refresh($args, $this);

        // Store
        $this->_storeToken($auth);

        return ( $this->token === null ? false : true );
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
        if ( ! in_array($method, $this->methods) ) {
            throw new InvalidRequest('Invalid request type ('.$method.')');
        }

        // Check token
        if ( $this->token === null ) {
            throw new InvalidRequest('You haven\'t authenticated yet');
        }

        // Check token expiration
        if ( $this->expires !== null and time() > $this->expires ) {
            throw new InvalidRequest('Your current OAuth session has expired');
        }

        // Append URL
        if ( $method == 'GET' and ! empty($data) ) {
        	$url .= '?'.http_build_query($data);
        	$data = array();
        }

        // Start request
        $this->request->setup($url, $method, $data, $this->token);

        // Make request
        $result = $this->request->make();

        // Check response
        $result = json_decode($result, true);

        // Check JSON for error
        if ( isset($result['status']) and ! $result['status'] ) {
        	$error = ( isset($result['errors']) ? implode("\n", $result['errors']) : $result['error'] );
            throw new InvalidResponse($error);
        }

        // Return response
        return $result;
    }

    public function __call($method, $args)
    {
        // Variables
        $method = strtoupper($method);
        $url    = $this->url.$this->version.'/'.$args[0];
        $post   = ( isset($args[1]) ? $args[1] : array() );

        // Make request
        return $this->_request($url, $method, $post);
    }

}
