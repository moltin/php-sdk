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

class Payment
{
	protected static $sdk;

	public static function init(\Moltin\SDK\SDK $sdk)
	{
		self::$sdk = $sdk;
	}

	public static function Authorize($order, $data = array())
	{
		return self::Process('authorize', $order, $data);
	}

	public static function CompleteAuthorize($order, $data = array())
	{
		return self::Process('complete_authorize', $order, $data);
	}

	public static function Capture($order, $data = array())
	{
		return self::Process('capture', $order, $data);
	}

	public static function Purchase($order, $data = array())
	{
		return self::Process('purchase', $order, $data);
	}

	public static function CompletePurchase($order, $data = array())
	{
		return self::Process('complete_purchase', $order, $data);
	}

	public static function Refund($order, $data = array())
	{
		return self::Process('refund', $order, $data);
	}

	public static function Void($order, $data = array())
	{
		return self::Process('void', $order, $data);
	}

	protected static function Process($method, $order, $data = array())
	{
		return self::$sdk->post('checkout/payment/'.$method.'/'.$order, $data);
	}
}
