<?php

namespace Moltin\Resources;

use Moltin\Resource as Resource;

class Flows extends Resource
{
    public $uri = 'flows';

    public function createField($flowId, $payload)
    {
        $payload['relationships']['flow']['data'] = [
            'type' => 'flow',
            'id' => $flowId
        ];

        return $this->getClient()->fields->create($payload);
    }

}
