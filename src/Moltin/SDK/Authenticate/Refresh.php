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

namespace Moltin\SDK\Authenticate;

use Moltin\SDK\Exception\InvalidResponseException as InvalidResponse;

class Refresh implements \Moltin\SDK\AuthenticateInterface
{
    protected $data = array(
        'token'   => null,
        'refresh' => null,
        'expires' => null
    );

    public function authenticate($args, \Moltin\SDK\SDK $parent)
    {
        // Check refresh token
        if ( ! isset($args['refresh_token']) or empty($data['refresh_token'])) {
            return false;
        }

        // Variables
        $url  = $parent->url . 'oauth/access_token';
        $data = array(
            'grant_type'    => 'refresh_token',
            'client_id'     => $args['client_id'],
            'client_secret' => $args['client_secret'],
            'refresh_token' => $args['refresh_token']
        );

        // Make request
        $parent->request->setup($url, 'POST', $data);
        $result = $parent->request->make();

        // Check response
        $result = json_decode($result, true);

        // Check JSON for error
        if (isset($result['error'])) {
            throw new InvalidResponse($result['error']);
        }

        // Set data
        $this->data['token']   = $result['access_token'];
        $this->data['refresh'] = $args['refresh_token'];
        $this->data['expires'] = $result['expires'];
    }

    public function get($key)
    {
        if ( ! isset($this->data[$key])) {
            return;
        }

        return $this->data[$key];
    }
}
