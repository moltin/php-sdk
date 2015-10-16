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

class Modifier
{
	protected static $sdk;

	public static function init(\Moltin\SDK\SDK $sdk)
	{
		self::$sdk = $sdk;
	}

	public static function Get($product, $modifier)
	{
		return self::$sdk->get('products/'.$product.'/modifiers/'.$modifier);
	}

	public static function Create($product, $data)
	{
		return self::$sdk->post('products/'.$product.'/modifiers', $data);
	}

	public static function Update($product, $modifier, $data)
	{
		return self::$sdk->put('products/'.$product.'/modifiers/'.$modifier, $data);
	}

	public static function Fields($product, $modifier = null)
	{
		return self::$sdk->fields('products/'.$product.'/modifiers'. (($modifier) ? '/'.$modifier : '') );
	}

	public static function Delete($product, $modifier)
	{
		return self::$sdk->delete('products/'.$product.'/modifiers/'.$modifier);
	}
}
