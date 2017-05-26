<?php

namespace Moltin\Resources;

use Moltin\Resource as Resource;

class Brands extends Resource
{
    public $uri = 'brands';

    /**
     *  Get the full brand tree
     *
     *  @return Moltin\Response
     */
    public function tree()
    {
        return $this->call('get', false, 'tree');
    }
}
