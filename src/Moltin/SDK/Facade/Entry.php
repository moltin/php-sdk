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

class Entry
{
	protected static $sdk;

	public static function init(\Moltin\SDK\SDK $sdk)
	{
		self::$sdk = $sdk;
	}

	public static function Get($flow, $id)
	{
		return self::$sdk->get('flows/'.$flow.'/entries/'.$id);
	}

	public static function Find($flow, $terms = array())
	{
		return self::$sdk->get('flows/'.$flow.'/entries/search', $terms);
	}

	public static function Listing($flow = null, $terms = array())
	{
		return self::$sdk->get('flows/'.$flow.'/entries', $terms);
	}	

	public static function Create($flow, $data)
	{
		return self::$sdk->post('flows/'.$flow.'/entries', $data);
	}

	public static function Update($flow, $id, $data)
	{
		return self::$sdk->put('flows/'.$flow.'/entries/'.$id, $data);
	}

	public static function Fields($flow = null, $id)
	{
		return self::$sdk->fields('flows/'.$flow.'/entries', $id);
	}

	public static function Delete($flow, $id)
	{
		return self::$sdk->delete('flows/'.$flow.'/entries/'.$id);
	}
}
