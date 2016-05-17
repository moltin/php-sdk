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
		return self::$sdk->get('customers/'.$customer.'/addresses/'.$id);
	}

	public static function Create($customer, $data)
	{
		return self::$sdk->post('customers/'.$customer.'/addresses', $data);
	}

	public static function Update($customer, $id, $data)
	{
		return self::$sdk->put('customers/'.$customer.'/addresses/'.$id, $data);
	}

	public static function Find($customer, $terms = array())
	{
		return self::$sdk->get('customers/'.$customer.'/addresses', $terms);
	}

	public static function Listing($customer, $terms = array())
	{
		return self::$sdk->get('customers/'.$customer.'/addresses', $terms);
	}

	public static function Delete($customer, $id)
	{
		return self::$sdk->delete('customers/'.$customer.'/addresses/'.$id);
	}

	public static function Fields($customer = null, $id = null)
	{
		$uri = 'customers';
		
		if ( $customer > 0 and $id === null ) { $uri .= '/'.$customer.'/addresses'; }
		else if ( $customer > 0 and $id > 0 ) { $uri .= '/'.$customer.'/addresses/'.$id; }
		else if ( $customer === null and $id > 0) { $uri = 'addresses/'.$id; }
		else { $uri = 'addresses'; }

		return self::$sdk->fields($uri);
	}

}
