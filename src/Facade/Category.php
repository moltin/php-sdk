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

class Category
{
	use \Moltin\SDK\FacadeTrait;

	protected static $single = 'categories';
	protected static $plural = 'categories';

	public static function Tree($terms = array())
	{
		return self::$sdk->get(self::$plural.'/tree', $terms);
	}

	public static function Order($data)
	{
		return self::$sdk->put(self::$plural.'/order', $data);
	}
}
