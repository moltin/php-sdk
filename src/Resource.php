<?php

namespace Moltin;

use Moltin\Client as Client;
use Moltin\Request as Request;
use Moltin\Session as Session;

class Resource
{
    // whether the resource requires authentication
    private $requiresAuthentication = true;

    // a map of plural => single types for relationships
    protected $relationshipTypeMap = [
        'brands' => 'brand',
        'categories' => 'category',
        'children' => 'category',
        'collections' => 'collection',
        'files' => 'file',
        'parent' => 'category',
        'products' => 'product'
    ];

    // the \Moltin\Client
    private $client;
    // the \Moltin\Interfaces\Storage concrete implementation
    private $storage;
    // the \Moltin\Request
    private $requestLib;

    // call params
    private $sort;

    // int
    private $limit;
    private $offset;

    // response from the API
    private $response;

    /**
     *  Create and return a new Resource
     *
     *  @param Client $client the Moltin\Client to use for calls
     *  @param Moltin\Request
     *  @param Moltin\Interfaces\Storage $storage a concrete implementation of the storage
     *  @return $this
     */
    public function __construct(Client $client, $requestLib = false, $storage = false)
    {
        $this->client = $client;
        $this->requestLib = $requestLib ? $requestLib : new Request;
        $this->storage = $storage ? $storage : new Session;
        return $this;
    }

    /**
     *  @todo implement
     *  @return $this
     */
    public function filter()
    {
        return $this;
    }

    /**
     *  Adds a sort parameter to the request (eg `-name` or `name,-slug`)
     *
     *  @return $this
     */
    public function sort($sort)
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     *  Set a limit on the number of resources
     *
     *  @param int $limit
     *  @return $this
     */
    public function limit($limit = false)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     *  Set an offset on the resources
     *
     *  @param int $offset
     *  @return $this
     */
    public function offset($offset = false)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     *  Get a resources attributes
     *
     *  @return Moltin\Response
     */
    public function attributes()
    {
        return $this->call('get', false, 'attributes');
    }

    /**
     *  Get a resource(s)
     *
     *  @param string|false $id the ID of a specific resource
     *  @return Moltin\Response
     */
    public function get($id = false)
    {
        return $this->call('get', false, $id);
    }

    /**
     *  Delete a resource
     *
     *  @param string the ID of the resource to delete
     *  @return Moltin\Response
     */
    public function delete($id)
    {
        return $this->call('delete', false, $id);
    }

    /**
     *  Update a resource
     *
     *  @param string $id the UUID of the resource to update
     *  @param array $data the data to update the resource with
     *  @return Moltin\Response
     */
    public function update($id, $data)
    {
        return $this->call('put', $data, $id);
    }

    /**
     *  Create a new resource
     *
     *  @param array $data the data to create the resource with
     *  @return Moltin\Response
     */
    public function create($data)
    {
        return $this->call('post', ['data' => $data]);
    }

    /**
     *  Create relationships from a resource to other resources
     *
     *  @param string $from the UUID of the resource you're creating a relationship on
     *  @param string $to the resource type to create a link to (use the plural, eg 'categories' rather than 'category')
     *  @param array|string|null $ids the $ids to create relationships to
     */
    public function createRelationships($from, $to, $ids)
    {
        return $this->makeRelationshipCall('post', $from, $to, $ids);
    }

    /**
     *  Update relationships from a resource to other resources
     *
     *  @param string $from the UUID of the resource you're updating a relationship on
     *  @param string $to the resource type to update links to (use the plural, eg 'categories' rather than 'category')
     *  @param array|string|null $ids the $ids to create relationships to
     */
    public function updateRelationships($from, $to, $ids = null)
    {
        return $this->makeRelationshipCall('put', $from, $to, $ids);
    }

    /**
     *  Delete relationships from a resource to other resources
     *
     *  @param string $from the UUID of the resource you're deleting a relationship on
     *  @param string $to the resource type to delete links to (use the plural, eg 'categories' rather than 'category')
     *  @param array|null $ids the $ids to delete relationships to
     */
    public function deleteRelationships($from, $to, $ids = null)
    {
        return $this->makeRelationshipCall('delete', $from, $to, $ids);
    }


    public function makeRelationshipCall($method, $from, $to, $ids)
    {
        if (!($type = $this->getRelationshipType($to))) {
            throw new Exceptions\InvalidRelationshipTypeException;
        }

        $body = ['data' => $this->buildRelationshipData($type, $ids)];

        return $this->call($method, $body, "$from/relationships/$to");
    }

    /**
     *  Given a string (plural, eg 'categories') return the `type` of resource
     *
     *  @param string $to the pluralised type
     *  @return string|false the type if found, false if not
     */
    public function getRelationshipType($to)
    {
        $map = $this->relationshipTypeMap;
        if (isset($map[$to])) {
            return $map[$to];
        }

        return false;
    }

    /**
     *  build the body data for a relationship call
     *
     *  @param string $type the type of resource you're relating to (eg 'category')
     *  @param array $ids an array of UUID's for the resources you're relating to
     *  @return array|null if an array of id's is valid return them, otherwise retun null
     */
    public function buildRelationshipData($type, $ids)
    {
        if ($ids === null || (is_array($ids) && empty($ids))) {
            return null;
        }

        // one relationship to add
        if (is_string($ids)) {
            return [
                'type' => $type,
                'id' => $ids
            ];
        }

        // many relationships to add
        $data = [];
        if(!empty($ids)) {
            foreach($ids as $id) {
                $data[] = [
                    'type' => $type,
                    'id' => $id
                ];
            }
        }
        return $data;
    }

    /**
     *  Get an access token from the local storage if available, otherwise request one from the API
     *
     *  @return string the access token
     *  @throws Exceptions\AuthenticationException
     */
    public function getAccessToken()
    {
        // check in the session
        $existing = $this->storage->getKey('authentication');

        // is it still valid
        if ($existing && $existing->expires > time()) {
            return $existing->access_token;
        }

        // make the call to the API
        $authResponse = $this->makeAuthenticationCall();

        // save the access token result
        $this->storage->setKey('authentication', $authResponse->getRaw());

        // return the token
        return $authResponse->getRaw()->access_token;
    }

    /**
     *  Get an access token from the API
     *
     *  @return Moltin\Response
     *  @throws Exceptions\AuthenticationException
     */
    public function makeAuthenticationCall()
    {
        $authResponse = $this->call('POST', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client->getClientID(),
            'client_secret' => $this->client->getClientSecret()
        ], false, ['Content-Type' => 'application/x-www-form-urlencoded'], false, false);

        if (empty($authResponse->getRaw()->access_token)) {
            throw new Exceptions\AuthenticationException;
        }

        return $authResponse;
    }

    /**
     *  Make a call to the API
     *
     *  @param string $method request method to use GET|POST|PUT|PATCH|DELETE
     *  @param array $body any body data to send with the request
     *  @param string $uriAppend any additional URI componenents as a string (eg 'relationships/categories')
     *  @param array $headers any specific headers for the request
     *  @param bool $requiresAuthentication true if the call requires authentication (true for all calls except auth)
     *  @param bool $buildQueryParams should we build query params (sort, limit etc)
     *  @return Moltin\Response
     */
    public function call($method, $body = false, $uriAppend = false, $headers = [], $requiresAuthentication = true, $buildQueryParams = true)
    {
        $headers = $this->addRequestHeaders($headers);

        $url = $requiresAuthentication ? $this->client->getAPIEndpoint($this->uri) : $this->client->getAuthEndpoint();
        if ($uriAppend) {
            $url = $url . '/' . $uriAppend;
        }

        $request = clone $this->requestLib;
        $request->setURL($url)
            ->setMethod($method)
            ->addHeaders($headers)
            ->setBody($body);

        if ($buildQueryParams) {
            $request->setQueryStringParams($this->buildQueryStringParams());
        }

        if ($requiresAuthentication) {
            $request->addHeader('Authorization', $this->getAccessToken());
        }

        return $request->make()->getResponse();
    }

    /**
     *  Adds moltin specific request headers to an array to be passed to the request
     *
     *  @param array $headers
     *  @return array
     */
    public function addRequestHeaders($headers)
    {
        $currency = $this->client->getCurrencyCode();
        if (!empty($currency)) {
            $headers['X-MOLTIN-CURRENCY'] = $this->client->getCurrencyCode();
        }
        return $headers;
    }

    /**
     *  Build the query string parameters based on the resource settings
     *
     *  @return array
     */
    public function buildQueryStringParams()
    {
        $params = [];
        if ($this->limit > 0) {
            $params['page']['limit'] = $this->limit;
        }
        if ($this->offset > 0) {
            $params['page']['offset'] = $this->offset;
        }
        if ($this->sort) {
            $params['sort'] = $this->sort;
        }
        return $params;
    }

}
