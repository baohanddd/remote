<?php
namespace baohan\Remote;

use baohan\Remote\Response\Document;
use GuzzleHttp\Client;

abstract class Remote
{
    /**
     * Http client
     *
     * @var \GuzzleHttp\Client
     */
    private $http;

    /**
     * Resource URI
     *
     * @var string
     */
    protected $uri;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $res;

    /**
     * It's prefix of request uri
     *
     * @var string
     */
    protected $prefix = '';

    public function __construct()
    {
        $cfg = $this->getConfig();
        if(!$cfg instanceof Config)
            throw new \RuntimeException('It must be instance of baohan\Remote\Config returned by getConfig()');
        $this->uri = $this->getURI();

        $this->http = new Client([
            'base_uri'    => $cfg->getHost(),
            'timeout'     => $cfg->getTimeout() ?: 5.0,
            'verify'      => $cfg->enableVerify()  ?: false,
            'http_errors' => $cfg->enableHttpErrors() ?: false,
            'debug'       => $cfg->enableDebug() ?: false,
        ]);
        $this->prefix = $cfg->getPrefix();
    }

    /**
     * Get configure data
     *
     * @return Config
     */
    abstract public function getConfig();

    /**
     * Get resource name
     *
     * @return string
     */
    abstract public function getURI();

    /**
     * Find by criteria and return collection
     *
     * @param array $criteria
     * @return Collection
     */
    public function find($criteria = [])
    {
        $this->res = $this->http->get($this->uri(), ['query' => $criteria]);
        return $this->document($this->json(true));
    }

    /**
     * Find by criteria and return the first element
     *
     * @param array $criteria
     * @return Document
     */
    public function findFirst($criteria = [])
    {
        $criteria['limit'] = 1;
        $criteria['page']  = 1;
        $this->res = $this->http->get($this->uri(), ['query' => $criteria]);
        return $this->document($this->json(true));
    }

    /**
     * Find by criteria and return collection
     *
     * @param array $criteria
     * @return Collection
     */
    public function count($criteria = [])
    {
        $this->res = $this->http->get($this->uri().'/count', ['query' => $criteria]);
        return $this->document($this->json(true));
    }

    /**
     * Find by id and return item
     *
     * @param $id
     * @return Collection
     */
    public function findById($id)
    {
        $this->res = $this->http->get($this->uri() . '/' . $id);
        return $this->document($this->json(true));
    }

    /**
     * Create new item
     *
     * @param array $data
     * @return bool
     */
    public function create($data = [])
    {
        $this->res = $this->http->post($this->uri(), ['form_params' => $data]);
        return $this->success();
    }

    /**
     * Update item by id
     *
     * @param $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data = [])
    {
        $this->res = $this->http->put($this->uri().'/'.$id, ['form_params' => $data]);
        return $this->success();
    }

    /**
     * patch item by id
     *
     * @param $id
     * @param array $data
     * @return bool
     */
    public function patch($id, $data = [])
    {
        $this->res = $this->http->patch($this->uri().'/'.$id, ['form_params' => $data]);
        return $this->success();
    }

    /**
     * Delete item by id
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $this->res = $this->http->delete($this->uri().'/'.$id);
        return $this->success();
    }

    /**
     * Delete found items by query
     *
     * @param array $query
     * @return bool
     */
    public function deletes($query = [])
    {
        $this->res = $this->http->delete($this->uri(), ['query' => $query]);
        return $this->success();
    }

    /**
     * @param array $json
     * @return Collection
     */
    private function document($json = [])
    {
        return $json ? new Document($json) : new Document();
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function error()
    {
        return $this->json()->message;
    }

    /**
     * Get JSON object
     *
     * @param bool|false $toArray
     * @return mixed
     */
    public function json($toArray = false)
    {
        return json_decode((string) $this->res->getBody(), $toArray);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->json()->_id;
    }

    /**
     * It would be return true if success
     *
     * @return bool
     */
    public function success()
    {
        $code = $this->res->getStatusCode();
        return in_array($code, [200, 201]);
    }

    /**
     * @return string
     */
    protected function uri()
    {
        return $this->prefix . $this->uri;
    }
}