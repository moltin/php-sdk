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

class Stats
{
	protected static $sdk;
	protected static $available = ['24hours', '7days', '30days'];

	public static function init(\Moltin\SDK\SDK $sdk)
	{
		self::$sdk = $sdk;
	}

	public static function Store($timeframe = null, $to = null)
	{
		return self::Stats('store', $timeframe, $to);
	}

	public static function Revenue($timeframe = null, $to = null)
	{
		return self::Stats('revenue', $timeframe, $to);
	}

	public static function Orders($timeframe = null, $to = null)
	{
		return self::Stats('orders', $timeframe, $to);
	}

	public static function Customers($timeframe = null, $to = null)
	{
		return self::Stats('customers', $timeframe, $to);
	}

	protected static function Stats($type, $timeframe = null, $to = null)
	{
		$data = [];
		
		if ( in_array($timeframe, self::$available) ) { $data['timeframe'] = $timeframe; }
		else if ( $timeframe !== null ) { $data['from'] = $timeframe; }

		if ( $to !== null ) { $data['to'] = $to; }

		return self::$sdk->get('statistics/'.$type, $data);
	}
}