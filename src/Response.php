<?php

namespace Moltin;

class Response
{

    /**
     *  @var int the http status code
     */
    private $statusCode;

    /**
     *  @var string the moltin request identifier
     */
    private $requestID;

    /**
     *  @var float the time taken to execute the call
     */
    private $executionTime;

    /**
     *  @var object the json decoded response in full
     */
    private $raw;

    /**
     *  @var array|object the responses data
     */
    private $data;

    /**
     *  @var array|object the included data resource
     */
    private $included;

    /**
     *  @var object the responses meta
     */
    private $meta;

    /**
     *  @var array the responses root level links
     */
    private $links = [];

    /**
     *  @var array the responses errors
     */
    private $errors = [];

    /**
     *  Get the raw json decoded response
     *
     *  @return object
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     *  Parse $this->raw and set on objects
     */
    public function parse()
    {
        if (isset($this->raw->data)) {
            $this->setData($this->raw->data);
        }

        if (isset($this->raw->included)) {
            $this->setIncluded($this->raw->included);
        }

        if (isset($this->raw->links)) {
            $this->setLinks($this->raw->links);
        }

        if (isset($this->raw->meta)) {
            $this->setMeta($this->raw->meta);
        }

        if (isset($this->raw->errors)) {
            $this->setErrors($this->raw->errors);
        }

        return $this;
    }

    /**
     *  Get the response data
     *
     *  @return object|array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     *  Get the included data
     *
     *  @return
     */
    public function included()
    {
        return $this->included;
    }

    /**
     *  Get the response errors
     *
     *  @return null|array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     *  Get the response meta
     *
     *  @return null|object
     */
    public function meta()
    {
        return $this->meta;
    }

    /**
     *  Get the response links
     *
     *  @return null|array
     */
    public function links()
    {
        return $this->links;
    }

    /**
     *  Get the request ID
     *
     *  @return string
     */
    public function getRequestID()
    {
        return $this->requestID;
    }

    /**
     *  Get the status code
     *
     *  @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     *  Get the execution time (including network latency)
     *
     *  @return float
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }

    /**
     *  Set the status code
     *
     *  @param int
     *
     *  @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     *  Set the moltin request ID
     *
     *  @param string
     *
     *  @return $this
     */
    public function setRequestID($id)
    {
        $this->requestID = $id;
        return $this;
    }

    /**
     *  Set the execution time
     *
     *  @param flaot
     *
     *  @return $this
     */
    public function setExecutionTime($time)
    {
        $this->executionTime = $time;
        return $this;
    }

    /**
     *  Set the raw response
     *
     *  @param object
     *
     *  @return $this
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;
        return $this;
    }

    /**
     *  Set the response data
     *
     *  @param array|object
     *
     *  @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     *  Set the included resource data
     *
     *  @param array|object
     *
     *  @return $this
     */
    public function setIncluded($included)
    {
        $this->included = $included;
        return $this;
    }

    /**
     *  Set the response meta
     *
     *  @param object
     *
     *  @return $this
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     *  Set the response links
     *
     *  @param array
     *
     *  @return $this
     */
    public function setLinks($links)
    {
        $this->links = $links;
        return $this;
    }

    /**
     *  Set the response errors
     *
     *  @param arrays
     *
     *  @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

}
