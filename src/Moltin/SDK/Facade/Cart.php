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

class Cart
{
	protected static $sdk;
	protected static $identifier;

	public static function init(\Moltin\SDK\SDK $sdk)
	{
		self::$sdk        = $sdk;
		self::$identifier = self::$sdk->identifier();
	}

	public static function Identifier()
	{
		return self::$identifier;
	}

	public static function Contents()
	{
		return self::$sdk->get('carts/'.self::$identifier);
	}

	public static function Insert($id, $qty = 1, $mods = null)
	{
		return self::$sdk->post('carts/'.self::$identifier, array(
			'id'       => $id,
			'quantity' => $qty,
			'modifier' => $mods
		));
	}

	public static function Update($id, $data)
	{
		return self::$sdk->put('carts/'.self::$identifier.'/item/'.$id, $data);
	}

	public static function Delete()
	{
		return self::$sdk->delete('carts/'.self::$identifier);
	}

	public static function Remove($id)
	{
		return self::$sdk->delete('carts/'.self::$identifier.'/item/'.$id);
	}

	public static function Item($id)
	{
		return self::$sdk->get('carts/'.self::$identifier.'/item/'.$id);
	}

	public static function InCart($id)
	{
		return self::$sdk->get('carts/'.self::$identifier.'/has/'.$id);
	}

	public static function Checkout()
	{
		return self::$sdk->get('carts/'.self::$identifier.'/checkout');
	}

	public static function Order($data = array())
	{
		return self::$sdk->post('carts/'.self::$identifier.'/checkout', $data);
	}

	public static function Discount($code = false)
	{
		if ( $code === false )
		{
			return self::$sdk->delete('cart/'.self::$identifier.'/discount');
		}

		return self::$sdk->post('cart/'.self::$identifier.'/discount', ['code' => $code]);
	}

	public static function Listing($terms = array())
	{
		return self::$sdk->get('carts', $terms);
	}
}
