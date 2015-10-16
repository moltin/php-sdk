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

class Transaction
{
    protected static $sdk;

    public static function init(\Moltin\SDK\SDK $sdk)
    {
        self::$sdk = $sdk;
    }

    public static function Get($slug)
    {
        return self::$sdk->get('transactions/' . $slug);
    }

    public static function Listing($terms = [])
    {
        return self::$sdk->get('transactions', $terms);
    }
}
