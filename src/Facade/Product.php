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

class Product
{
	use \Moltin\SDK\FacadeTrait;

	protected static $single = 'products';
	protected static $plural = 'products';

	public static function Search($terms = array())
	{
		return self::$sdk->get(self::$plural.'/search', $terms);
	}

	public static function Modifiers($id)
	{
		return self::$sdk->get(self::$single.'/'.$id.'/modifiers');
	}

	public static function Variations($id)
	{
		return self::$sdk->get(self::$plural.'/'.$id.'/variations');
	}
}
