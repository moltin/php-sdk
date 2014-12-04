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

class Address
{
	protected static $sdk;

	public static function init(\Moltin\SDK\SDK $sdk)
	{
		self::$sdk = $sdk;
	}

	public static function Get($customer, $id)
	{
		return self::$sdk->get('customer/'.$customer.'/address/'.$id);
	}

	public static function Create($customer, $data)
	{
		return self::$sdk->post('customer/'.$customer.'/address', $data);
	}

	public static function Update($customer, $id, $data)
	{
		return self::$sdk->put('customer/'.$customer.'/address/'.$id, $data);
	}

	public static function Find($customer, $terms = array())
	{
		return self::$sdk->get('customer/'.$customer.'/address', $terms);
	}

	public static function Listing($customer, $terms = array())
	{
		return self::$sdk->get('customer/'.$customer.'/addresses', $terms);
	}

	public static function Fields($customer = null, $id = null)
	{
		$uri = 'customer';
		
		if ( $customer > 0 and $id === null ) { $uri .= '/'.$customer.'/address'; }
		else if ( $customer > 0 and $id > 0 ) { $uri .= '/'.$customer.'/address/'.$id; }

		return self::$sdk->fields($uri);
	}

}
