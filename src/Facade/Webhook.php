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

class Webhook
{
	protected static $sdk;

	public static function init(\Moltin\SDK\SDK $sdk)
	{
		self::$sdk = $sdk;
	}

	public static function Get($id)
	{
		return self::$sdk->get('webhooks/'.$id);
	}

	public static function Listing($terms = array())
	{
		return self::$sdk->get('webhooks', $terms);
	}

	public static function Create($data)
	{
		return self::$sdk->post('webhooks', $data);
	}

	public static function Update($id, $data)
	{
		return self::$sdk->put('webhooks/'.$id, $data);
	}

	public static function Delete($id)
	{
		return self::$sdk->delete('webhooks/'.$id);
	}

	public static function Fields($id = null)
	{
		return self::$sdk->fields('webhooks', $id);
	}

}
