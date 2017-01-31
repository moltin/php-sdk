<?php

namespace Moltin;

use Moltin\Entities\Cart as Cart;

class Client
{
    const UA = 'moltin-php-sdk/2';

    // API endpoint configs
    private $version = 'v2';
    private $base = 'https://api.moltin.com';
    private $authURI = 'oauth/access_token';

    // Authentication Params
    private $client_id;
    private $client_secret;

    // Store Configuration
    private $currency_code;
    private $language;
    private $locale;

    // the name of the cart reference in the $_COOKIE
    private $cookieCartName = 'moltin_cart_reference';
    // the lifetime of the $_COOKIE : must be compatible with strtotime() - http://php.net/manual/en/function.strtotime.php
    private $cookieLifetime = '+1 week';

    /**
     *  __get overloads the client with a property that will check if there is a resource for the given $method
     *  which allows calls such as $moltin->products->get() to be correctly routed to the appropriate handler
     */
    public function __get($method)
    {
        $potentialEndpointClass = 'Moltin\Resources\\' . ucfirst($method);
        if (class_exists($potentialEndpointClass)) {
            // construct a resource object and pass in this client
            $resource = new $potentialEndpointClass($this);
            return $resource;
        }

        $trace = debug_backtrace();
        $message = 'Undefined property via __get(): ' . $method . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'];
        throw new Exceptions\InvalidResourceException($message);
    }

    /**
     *  Create an instance of the SDK, passing in a configuration for it to set up
     *
     *  @param array intitial config
     *
     *  @return $this
     */
    public function __construct($config = [])
    {
        if (isset($config['client_id'])) {
            $this->setClientID($config['client_id']);
        }
        if (isset($config['client_secret'])) {
            $this->setClientSecret($config['client_secret']);
        }
        if (isset($config['currency_code'])) {
            $this->setCurrencyCode($config['currency_code']);
        }
        if (isset($config['language'])) {
            $this->setLanguage($config['language']);
        }
        if (isset($config['locale'])) {
            $this->setLocale($config['locale']);
        }
        if (isset($config['api_endpoint'])) {
            $this->setBaseURL($config['api_endpoint']);
        }
        if (isset($config['cookie_cart_name'])) {
            $this->setCookieCartName($config['cookie_cart_name']);
        }
        if (isset($config['cookie_lifetime'])) {
            $this->setCookieLifetime($config['cookie_lifetime']);
        }
        return $this;
    }

    /**
     *  Set a custom base URL to access the API (for enterprise customers)
     *
     *  @param string $base the base URL (fully qualified, eg 'https://api.yourcompany.com')
     *  @return $this
     */
    public function setBaseURL($base)
    {
        $this->base = $base;
        return $this;
    }

    /**
     *  Set a custom cart name to use in the $_COOKIE
     *
     *  @param string $name
     *  @return $this
     */
    public function setCookieCartName($name)
    {
        $this->cookieCartName = $name;
        return $this;
    }

    /**
     *  Get the cookie name
     *
     *  @return string
     */
    public function getCookieCartName()
    {
        return $this->cookieCartName;
    }


    /**
     *  Set a custom lifetime for the $_COOKIE
     *
     *  @param string $lifeTime must be compatible with strtotime() - http://php.net/manual/en/function.strtotime.php
     *  @return $this
     */
    public function setCookieLifetime($lifeTime)
    {
        $this->cookieLifetime = $lifeTime;
        return $this;
    }

    /**
     *  Get the cookie lifetime
     *
     *  @return string
     */
    public function getCookieLifetime()
    {
        return $this->cookieLifetime;
    }

    /**
     *  Get the authentication endpoint
     *
     *  @return string the FQDN with URI for authentication requests
     */
    public function getAuthEndpoint()
    {
        return $this->getBase() . '/' . $this->getAuthURI();
    }

    /**
     *  Get the API endpoint for non authentication calls
     *
     *  @param string $uri is the uri to append to the API endpoint
     *
     *  @return string the FQDN with URI for API requests
     */
    public function getAPIEndpoint($uri = false)
    {
        $endpoint = $this->getBase() . '/' . $this->getVersion() . '/';

        if ($uri) {
            $endpoint .= $uri;
        }

        return $endpoint;
    }

    /**
     *  Get a cart - if $cartID is not provided a new cart will be created
     *
     *  @param string|false $cartID
     *  @return Moltin\Entities\Cart
     */
    public function cart($cartID = false)
    {
        return new Cart($cartID, $this);
    }

    /**
     *  Get the currency code
     *
     *  @return string
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     *  Get the language
     *
     *  @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     *  Get the locale
     *
     *  @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     *  Get the API version
     *
     *  @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     *  Get the base URL
     *
     *  @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     *  Get the URI to authenticate against
     *
     *  @return string
     */
    public function getAuthURI()
    {
        return $this->authURI;
    }

    /**
     *  Get the client_id
     */
    public function getClientID()
    {
        return $this->client_id;
    }

    /**
     *  Get the client_secret
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     *  Set the client_id for authentication calls
     *
     *  @param string the client_id
     *
     *  @return $this
     */
    public function setClientID($client_id)
    {
        $this->client_id = $client_id;
        return $this;
    }

    /**
     *  Set the client_secret for authentication calls
     *
     *  @param string the secret
     *
     *  @return $this
     */
    public function setClientSecret($secret)
    {
        $this->client_secret = $secret;
        return $this;
    }

    /**
     *  Set the requested currency code
     *
     *  @param string the currency code
     *  @return $this
     */
    public function setCurrencyCode($code)
    {
        $this->currency_code = $code;
        return $this;
    }

    /**
     *  Alias for $this->setCurrencyCode($code)
     *
     *  @param string the currency code
     *  @return $this
     */
    public function currency($code)
    {
        return $this->setCurrencyCode($code);
    }

    /**
     *  Set the requested language
     *
     *  @param string the language code
     *
     *  @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     *  Set the requested locale
     *
     *  @param string the locale code
     *
     *  @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

}
