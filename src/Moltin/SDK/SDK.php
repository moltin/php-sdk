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
    public $version  = 'beta';
    public $url      = 'https://api.molt.in/';
    public $auth_url = 'http://auth.molt.in/';

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
        if (isset($args['version'])) {
            $this->version = $args['version'];
        }

        // Retrieve information
        $this->token   = $this->store->get('token');
        $this->refresh = $this->store->get('refresh');
        $this->expires = $this->store->get('expires');
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

    public function fields($type, $id = null, $wrap = false, $suffix = 'fields')
    {
        // Variables
        $fields = $this->get($type . ($id !== null ? '/' . $id : '') . '/' . $suffix);
        $flows = new Flows($fields['result'], $wrap);

        // Build and return form
        return $flows->build($fields);
    }

    public function identifier()
    {
        if (isset($_COOKIE['identifier'])) {
            return $_COOKIE['identifier'];
        }

        $identifier = md5(uniqid());
        setcookie('identifier', $identifier, strtotime("+30 day"), '/');

        return $identifier;
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
        }

        // Start request
        $this->request->setup($url, $method, $data, $this->token);

        // Make request
        $result = $this->request->make();

        // Check response
        $result = json_decode($result, true);

        // Check JSON for error
        if (isset($result['status']) and ! $result['status']) {

            // Format errors
            if (isset($result['errors']) && is_array($result['errors'])) {
                $error = implode("\n", $result['errors']);
            } elseif (isset($result['error']) && is_array($result['error'])) {
                $error = implode("\n", $result['error']);
            } else {
                $error = $result['error'];
            }

            throw new InvalidResponse($error);
        }

        // Return response
        return $result;
    }

    public function __call($method, $args)
    {
        // Variables
        $regex = "/(get|delete|update|create)(?:(\w+)By(\w+)|(\w+))/i";
        $map   = array('update' => 'put', 'create' => 'post');

        // Nice method generation
        if (preg_match($regex, $method, $result)) {
            // Format result
            if (isset($result[4])) {
                $type   = $result[1];
                $method = $result[4];
                $by     = null;
            } else {
                $type   = $result[1];
                $method = $result[2];
                $by     = $result[3];
            }

            // Sub-items
            $pieces = array_filter(preg_split('/(?=[A-Z])/', $method));
            $method = implode('/', $pieces);

            // Variables
            $type = strtoupper(( array_key_exists($type, $map) ? $map[$type] : $type ));
            $url  = $this->url . $this->version . '/' . strtolower($method);
            $post = array();

            // Append Id directly to URL
            if (isset($args[0]) and ! empty($args[0]) and ( ( $by !== null and $by == 'Id' ) or in_array($type, array('PUT', 'DELETE')) )) {
                $url .= '/' . $args[0];
                array_shift($args);
                $by = null;
            }

            // Append Identifier to Cart
            if (strtolower($method) == 'cart') {
                $url .= '/' . $this->identifier();
            }

            // Setup get-by
            if ($by !== null) {
                $post = array(strtolower($by) => $args[0]);
                array_shift($args);
            }

            // Set post
            if ( ! empty($args)) {
                $post = $args[0];
            }

        }
        // Base method request
        else {
            // Variables
            $type = strtoupper($method);
            $url  = $this->url.$this->version . '/' . $args[0];
            $post = ( isset($args[1]) ? $args[1] : array() );
        }

        // Make request
        return $this->_request($url, $type, $post);
    }
}
