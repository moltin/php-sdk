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

class Moltin
{
	protected static $methods = array('ClientCredentials', 'Password', 'Refresh');
	protected static $sdk;

	public static function init(\Moltin\SDK\SDK $sdk)
	{
		self::$sdk = $sdk;
	}

	public static function Authenticate($method, $data = array(), $extra = array())
	{
		if ( self::$sdk === null ) {
			self::$sdk = new \Moltin\SDK\SDK(new \Moltin\SDK\Storage\Session(), new \Moltin\SDK\Request\CURL(), $extra);
		}

		$method = '\\Moltin\\SDK\\Authenticate\\'.$method;
		return self::$sdk->authenticate(new $method(), $data);
	}

	public static function Get($uri, $data = array())
	{
		return self::$sdk->get($uri, $data);
	}

	public static function Post($uri, $data = array())
	{
		return self::$sdk->post($uri, $data);
	}

	public static function Put($uri, $data = array())
	{
		return self::$sdk->put($uri, $data);
	}

	public static function Delete($uri, $data = array())
	{
		return self::$sdk->delete($uri, $data);
	}

	public static function Fields($type, $id = null, $wrap = false, $suffix = 'fields')
	{
		return self::$sdk->fields($type, $id, $wrap, $suffix);
	}

	public static function Identifier()
	{
		return self::$sdk->identifier();
	}
}