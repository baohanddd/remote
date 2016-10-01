<?php
namespace baohan\Remote;

use App\Component\Collection\Document;
use App\Component\Collection\Documents;
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
        $config = $this->getConfig();
        $this->uri = $this->getURI();

        $this->http = new Client([
            'base_uri'    => $config['host'],
            'timeout'     => 5.0,
            'verify'      => false,
            'http_errors' => false,
            'debug'       => false,
        ]);
        $this->prefix = $config['prefix'];
    }

    /**
     * Get configure data
     *
     * @return array
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
     * @return \App\Component\Collection\Document[]
     */
    public function find($criteria = [])
    {
        $this->res = $this->http->get($this->uri(), ['query' => $criteria]);
        return $this->documents($this->json(true));
    }

    /**
     * Find by criteria and return the first element
     *
     * @param array $criteria
     * @return \App\Component\Collection\Document
     */
    public function findFirst($criteria = [])
    {
        $criteria['limit'] = 1;
        $criteria['page']  = 1;
        $this->res = $this->http->get($this->uri(), ['query' => $criteria]);
        $docs = $this->documents($this->json(true));
        if($docs) return $docs[0];
                  return new Document();
    }

    /**
     * Find by criteria and return collection
     *
     * @param array $criteria
     * @return \App\Component\Collection\Document
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
     * @return \App\Component\Collection\Document
     */
    public function findById($id)
    {
        $this->res = $this->http->get($this->uri().'/'.$id);
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
     * @param array $json
     * @return Documents
     */
    private function documents($json = [])
    {
        $res = new Documents($json);
        return $res->wrap();
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