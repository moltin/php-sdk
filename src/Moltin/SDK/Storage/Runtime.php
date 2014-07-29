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

class Runtime implements \Moltin\SDK\StorageInterface
{
    protected $items = array();

    /**
     * Creates session or respawns previous instance, also created the default
     * addresses item if it doesn't already exist.
     *
     * @param array [$args] Optional array of arguments
     */
    public function __construct($args = array())
    {
        // Create default item
        if (! isset($this->items)) {
            $this->items = array();
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
        if (! isset($this->items[$id])) {
            return;
        }

        return $this->items[$id];
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
        // Update
        if ($id !== null) {
            if (! isset($data['id'])) {
                $data['id'] = $id;
            }

            $this->items[$id] = array_merge($this->items[$id], $data);

        // Insert
        } else {
            $this->items[] = $data;
            $ids = array_keys($this->items);
            $id = end($ids);
            if (! isset($this->items[$id]['id'])) {
                $this->items[$id]['id'] = $id;
            }
        }

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
        unset($this->items[$id]);

        return $this;
    }
}
