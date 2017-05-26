<?php

namespace Moltin;

/**
 *  Session is the PHP session storage mechanism that will store authentication keys
 */
class Session implements Interfaces\Storage
{
    const SESSION_KEY = 'moltinSDK';

    public function __construct()
    {
        session_id() || session_start();
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        return $this;
    }

    public function getKey($key)
    {
        if (isset($_SESSION[self::SESSION_KEY][$key])) {
            return $_SESSION[self::SESSION_KEY][$key];
        }
        return false;
    }

    public function setKey($key, $value)
    {
        $_SESSION[self::SESSION_KEY][$key] = $value;
        return $this;
    }

    public function removeKey($key)
    {
        unset($_SESSION[self::SESSION_KEY][$key]);
        return $this;
    }

}
