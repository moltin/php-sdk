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

use Moltin\SDK\InvalidRequestException as InvalidRequest;
use Moltin\SDK\InvalidResponseException as InvalidResponse;

class SDK
{

	// Test Paths
	public $version  = 'beta';
	public $url      = 'http://api.dev.molt.in/';
	public $auth_url = 'http://auth.dev.molt.in/';

	// Live Paths
	// public $version  = 'v1';
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

	public function __construct(\Moltin\SDK\StorageInterface $store, \Moltin\SDK\RequestInterface $request)
	{
		// Make global
		$this->store   = $store;
		$this->request = $request;

		// Retrieve information
		$this->token   = $this->store->get('token');
		$this->refresh = $this->store->get('refresh');
		$this->expires = $this->store->get('expires');
	}	

	public function authenticate(\Moltin\SDK\AuthenticateInterface $auth, $args = array())
	{
		// Perform authentication
		$auth->authenticate($args, $this);

		// Get keys
		$this->token   = $auth->get('token');
		$this->refresh = $auth->get('refresh');
		$this->expires = $auth->get('expires');

		// Store them
		$this->store('token',   $this->token);
		$this->store('refresh', $this->refresh);
		$this->store('expires', $this->expires);
	}

	public function refresh()
	{

	}

	protected function _request($url, $method, $post)
	{
		// Check type
		if ( ! in_array($type, $this->methods) ) {
			throw new InvalidRequest('Invalid request type ('.$type.')');
		}

		// Check token
		if ( $this->token === null ) {
			throw new InvalidRequest('You haven\'t authenticated yet');
		}

		// Check token expiration
		if ( $this->expires !== null and time() > $this->expires ) {
			throw new InvalidRequest('Your current OAuth session has expired');
		}

		// Start request
		$this->request->setup($url, $method, $post, $this->token);

		// Make request
		list($result, $code) = $this->request->make();

		// Check code
		if ( $code !== 200 ) {
			throw new InvalidResponse('Your request to '.$url.' returned code '.$code);
		}

		// Check response
		$result = json_decode($result, true);

		// Check JSON for error
		if ( isset($result['error']) ) {
			throw new InvalidResponse($result['error']);
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
