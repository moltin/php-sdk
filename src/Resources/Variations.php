<?php

namespace Moltin\Resources;

use Moltin\Resource as Resource;

class Variations extends Resource
{
    public $uri = 'variations';

    public function createOption($variationID, $data)
    {
        $data['type'] = 'product-variation-option';
        return $this->call('POST', ['data' => $data], $variationID . '/variation-options');
    }

    public function createModifier($variationID, $optionID, $data)
    {
        $data['type'] = 'product-modifier';
        return $this->call('POST', ['data' => $data], $variationID . '/variation-options/' . $optionID . '/product-modifiers');
    }

}
