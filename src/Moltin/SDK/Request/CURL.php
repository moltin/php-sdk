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

namespace Moltin\SDK\Request;

class CURL implements \Moltin\SDK\RequestInterface
{

	protected $curl;

    public function setup($url, $method, $post = array(), $token = null)
    {
    	// Start curl
    	$this->curl = curl_init();

		// Add request settings
		curl_setopt_array($this->curl, array(
			CURLOPT_URL            => $url,
			CURLOPT_CUSTOMREQUEST  => $method,
			CURLOPT_HEADER         => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => 4
		));

		// Add post
		if ( ! empty($post) ) {
			curl_setopt($this->curl, CURLOPT_POST, true);
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($post));
		}

		// Add auth header
		if ( $token !== null ) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'Authorization: OAuth '.$this->token
			));
		}

    }

    public function make()
    {
   		// Make request
		$result = curl_exec($this->curl);
		$code   = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

		return [$result, $code];
    }

}
