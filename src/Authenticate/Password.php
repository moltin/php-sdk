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
namespace Moltin\SDK\Authenticate;

use Moltin\SDK\Exception\InvalidResponseException;

class Password implements \Moltin\SDK\AuthenticateInterface
{
    protected $data = [
        'token' => null,
        'refresh' => null,
        'expires' => null,
    ];

    public function authenticate($args, \Moltin\SDK\SDK $parent)
    {
        // Variables
        $url = $parent->url.'oauth/access_token';
        $data = [
            'grant_type' => 'password',
            'username' => $args['username'],
            'password' => $args['password'],
            'client_id' => $args['client_id'],
            'client_secret' => $args['client_secret'],
            'redirect_uri' => $args['redirect_uri'],
        ];

        $parent->request->setup($url, 'POST', $data);
        $result = $parent->request->make();

        // Check response
        $result = json_decode($result, true);

        // Check JSON for errors
        if (isset($result['errors'])) {
            $exception = null;
            if (is_array($result['errors'])) {
                foreach($result['errors'] as $k => $v) {
                    if (isset($exception)) {
                        $exception = new InvalidResponseException($v[0], 0, $exception);
                    } else {
                        $exception = new InvalidResponseException($v[0]);
                    }
                }
            } else {
                $exception = $result['errors'];
            }
            throw new InvalidResponseException($exception);
        }

        // Set data
        $this->data['token'] = $result['access_token'];
        $this->data['refresh'] = $result['refresh_token'];
        $this->data['expires'] = $result['expires'];
    }

    public function get($key)
    {
        if (!isset($this->data[$key])) {
            return;
        }

        return $this->data[$key];
    }
}
