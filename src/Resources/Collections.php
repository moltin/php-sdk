<?php

namespace Moltin\Resources;

use Moltin\Resource as Resource;

class Collections extends Resource
{
    public $uri = 'collections';

    /**
     *  Get the full collection tree
     *
     *  @return Moltin\Response
     */
    public function tree()
    {
        return $this->call('get', false, 'tree');
    }
}
