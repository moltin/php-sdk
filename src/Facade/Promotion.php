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
* @author Adam Sturrock <adam@moltin.com>
* @copyright 2015 Moltin Ltd.
* @version dev
* @link http://github.com/moltin/php-sdk
*
*/

namespace Moltin\SDK\Facade;

class Promotion
{
	use \Moltin\SDK\FacadeTrait;

	protected static $single = 'promotions/cart';
	protected static $plural = 'promotions/cart';

	public static function Search($terms = array())
	{
		return self::$sdk->get(self::$plural.'/search', $terms);
	}

}
