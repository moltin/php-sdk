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

class Order
{
	use \Moltin\SDK\FacadeTrait;

	protected static $single = 'orders';
	protected static $plural = 'orders';

	public static function Items($id)
	{
		return self::$sdk->get('orders/'.$id.'/items');
	}

	public static function AddItem($order, $data)
	{
		return self::$sdk->post('orders/'.$order.'/items', $data);
	}

	public static function UpdateItem($order, $data)
	{
		return self::$sdk->put('orders/'.$order.'/items', $data);
	}

	public static function RemoveItem($order, $id)
	{
		return self::$sdk->delete('orders/'.$order.'/items/'.$id);
	}
}
