<?php

namespace Moltin\Interfaces;

interface Storage
{

    public function getKey($key);
    public function setKey($key, $value);
    public function removeKey($key);

}
