<?php

/**
* This file is part of Moltin PHP-SDK, a PHP package which
* provides convinient and rapid access to the API.
*
* Copyright (c) 2013-2014 Moltin Ltd.
* http://github.com/moltin/php-sdk
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*
* @package moltin/php-sdk
* @author Jamie Holdroyd <jamie@molt.in>
* @copyright 2014 Moltin Ltd.
* @version dev
* @link http://github.com/moltin/php-sdk
*
*/

namespace Moltin\SDK\Facade;

class Email
{
	protected static $sdk;

	public static function init(\Moltin\SDK\SDK $sdk)
	{
		self::$sdk = $sdk;
	}

	public static function Get($slug)
	{
		return self::$sdk->get('emails/'.$slug);
	}

	public static function Listing($terms = array())
	{
		return self::$sdk->get('emails', $terms);
	}

	public static function Create($data)
	{
		return self::$sdk->post('emails', $data);
	}

	public static function Update($slug, $data)
	{
		return self::$sdk->put('emails/'.$slug, $data);
	}

	public static function Delete($slug)
	{
		return self::$sdk->delete('emails/'.$slug);
	}
}
