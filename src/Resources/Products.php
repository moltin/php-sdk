<?php

namespace Moltin\Resources;

use Moltin\Resource as Resource;

class Products extends Resource
{
    public $uri = 'products';

    public function build($id)
    {
        return $this->call('POST', [], $id . '/build');
    }
}
