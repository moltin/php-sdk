<?php

/**
* This file is part of Moltin PHP-SDK, a PHP package which
* provides convinient and rapid access to the API.
*
* Copyright (c) 2013 Moltin Ltd.
* http://github.com/moltin/php-sdk
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*
* @package moltin/php-sdk
* @author Jamie Holdroyd <jamie@molt.in>
* @copyright 2013 Moltin Ltd.
* @version dev
* @link http://github.com/moltin/php-sdk
*
*/

namespace Moltin\SDK\Storage;

class Session implements \Moltin\SDK\StorageInterface
{
    /**
     * Creates session or respawns previous instance, also created the default
     * addresses item if it doesn't already exist.
     *
     * @param array [$args] Optional array of arguments
     */
    public function __construct($args = array())
    {
        session_id() or session_start();

        // Create default item
        if ( ! isset($_SESSION['sdk'])) {
            $_SESSION['sdk'] = array();
        }
    }

    /**
     * Retrieves the given item by id
     *
     * @param  integer    $id The id to query by
     * @return array|null
     */
    public function get($id)
    {
        // Not found
        if ( ! isset($_SESSION['sdk'][$id])) {
            return;
        }

        return $_SESSION['sdk'][$id];
    }

    /**
     * Inserts data or updates if id is provided.
     *
     * @param  integer [$id] The id to update
     * @param  array $data The data to insert/update
     * @return $this
     */
    public function insertUpdate($id = null, $data)
    {
        $_SESSION['sdk'][$id] = $data;

        return $this;
    }

    /**
     * Removes an object with the given id from storage.
     *
     * @param  integer $id
     * @return $this
     */
    public function remove($id)
    {
        unset($_SESSION['sdk'][$id]);

        return $this;
    }
}
