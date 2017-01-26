<?php

namespace Moltin\Resources;

use Moltin\Resource as Resource;

class Categories extends Resource
{
    public $uri = 'categories';

    /**
     *  Get the full category tree
     *
     *  @return Moltin\Response
     */
    public function tree()
    {
        return $this->call('get', false, 'tree');
    }
}
