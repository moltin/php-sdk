<?php

namespace Moltin;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\MultipartStream as MultipartStream;
use Moltin\Client as Client;

class Request
{

    // an HTTP client to execute the API calls (guzzle)
    private $httpClient;

    // string the request method GET|POST|PUT|PATCH|DELETE
    private $method;

    // string the URL
    private $url;

    // array request headers
    private $headers = [];

    // false|array request body
    private $body = false;

    // array URL params
    private $params = [];

    public function __construct($client = false)
    {
        $this->httpClient = $client ? $client : new HttpClient();
        return $this;
    }

    /**
     *  @param string $method the request method for the call
     *  @return $this
     *  @throws Moltin\Exceptions\InvalidRequestMethod
     */
    public function setMethod($method)
    {
        $method = strtoupper(trim($method));
        if (!in_array($method, ['GET','POST','PUT','PATCH','DELETE'])) {
            throw new Exceptions\InvalidRequestMethod;
        }
        $this->method = $method;
        return $this;
    }

    /**
     *  @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     *  @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     *  Get a specific header
     *
     *  @param string $name the header name
     *  @return false|string false if the header is not set, otherwise the string value
     */
    private function getHeader($name)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }
        return false;
    }

    /**
     *  Clear request headers
     *
     *  @return $this
     */
    public function clearHeaders()
    {
        $this->headers = [];
        return $this;
    }

    /**
     *  Add headers to the request
     *
     *  @param array $headers an array of $name => $value headers to set
     *  @return $this
     */
    public function addHeaders($headers)
    {
        foreach($headers as $name => $value) {
            $this->addHeader($name, $value);
        }
        return $this;
    }

    /**
     *  Add (over overwrite) a single header to the request
     *
     *  @param string $name the header name
     *  @param string $value the header value
     *  @return $this
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     *  Set the default request headers
     *
     *  @return $this
     */
    private function setDefaultHeaders()
    {
        $defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' =>  Client::UA,
            'X-MOLTIN-SDK-LANGUAGE' => 'php',
            'X-MOLTIN-SDK-VERSION' => 'v2-dev'
        ];

        foreach($defaultHeaders as $name => $value) {
            if (!$this->getHeader($name)) {
                $this->addHeader($name, $value);
            }
        }

        return $this;
    }

    /**
     *  Set the body of the request
     *
     *  @param array $body
     *  @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     *  Get the request body
     *
     *  @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     *  Get the request URL
     *
     *  @return string
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     *  Set the request URL
     *
     *  @param string $url the URL this request should hit
     *  @return $this
     */
    public function setURL($url)
    {
        $this->url = trim($url);
        return $this;
    }

    /**
     *  @return array
     */
    public function getPayload()
    {
        $payload = [];
        $body = $this->getBody();
        if (!empty($body)) {
            $payload[$this->getBodyKey()] = $body;
        }
        $payload['headers'] = $this->getHeaders();
        $params = $this->getQueryStringParams();
        if (!empty($params)) {
            $payload['query'] = $params;
        }

        // when sending multipart, specify our boundary and stream the data
        if ($this->getHeader('Content-Type') === 'multipart/form-data') {
            $payload = $this->prepareMultipartPayload($payload);
        }

        return $payload;
    }

    public function prepareMultipartPayload($payload)
    {
        // generate a random boundary
        $boundary = 'moltin_file_upload_' . rand(50000, 60000);

        // specify the boundary in the content type header
        $contentType = 'multipart/form-data; boundary=' . $boundary;
        $this->addHeader('Content-Type', $contentType);
        $payload['headers']['Content-Type'] = $contentType;

        // remove the multipart 
        $payload['body'] = new MultipartStream($payload['body'], $boundary);

        return $payload;
    }

    /**
     *  Set the query string params
     *
     *  @param array $params
     *  @return $this
     */
    public function setQueryStringParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     *  Get the query string params
     *
     *  @return array
     */
    public function getQueryStringParams()
    {
        return $this->params;
    }

    /**
     *  Get the payload key for the body depending on whether the call is JSON/multipart
     *
     *  @return string
     *  @throws Moltin\Exceptions\InvalidContentType
     */
    public function getBodyKey()
    {
        switch($this->getHeader('Content-Type')) {
            case 'application/json':
                return'json';
                break;
            case 'application/x-www-form-urlencoded':
                return 'form_params';
                break;
            case 'multipart/form-data':
                return 'body';
                break;
            default:
                throw new Exceptions\InvalidContentType;
        }
    }

    /**
     *  @return Moltin\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     *  Make a request
     *
     *  @return $this
     */
    public function make()
    {
        $this->setDefaultHeaders();

        $startTime = microtime(true);
        $result = $this->httpClient->request($this->getMethod(), $this->getURL(), $this->getPayload());
        $endTime = microtime(true);

        $this->response = new Response();
        $this->response->setExecutionTime(round(($endTime - $startTime), 5))
            ->setStatusCode($result->getStatusCode());

        // set the request ID for remote debugging if it is present            
        if (!empty(($requestID = $result->getHeader('X-Moltin-Request-Id')))) {
            $this->response->setRequestID($requestID[0]);
        }

        $body = json_decode($result->getBody());
        $this->response->setRaw($body)->parse();

        return $this;
    }

}
