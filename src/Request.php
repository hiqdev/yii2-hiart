<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use GuzzleHttp\Psr7\Request as Worker;

class Request implements \Serializable
{
    protected $builder;

    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var string Connection name
     */
    protected $dbname;

    /**
     * @var array request method
     */
    protected $method;
    protected $uri;
    protected $headers = [];
    protected $body;
    protected $version;

    public function __construct(QueryBuilder $builder, Query $query)
    {
        $this->builder = $builder;
        $this->query = $query;
    }

    public function getDbname()
    {
        return $this->dbname;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getWorker()
    {
        if ($this->worker === null) {
            if (!empty($this->query)) {
                $this->updateFromQuery();
            }
            $this->worker = $this->createWorker();
        }

        return $this->worker;
    }

    public function getQuery()
    {
        return $this->query;
    }

    protected function updateFromQuery()
    {
        $this->builder->prepare($this->query);

        $this->buildDbname();
        $this->buildAuth();
        $this->buildMethod();
        $this->buildUri();
        $this->buildQueryParams();
        $this->buildHeaders();
        $this->buildBody();
        $this->buildFormParams();
        $this->buildProtocolVersion();
    }

    protected function createWorker()
    {
        return new Worker($this->method, $this->uri, $this->headers, $this->body, $this->version);
    }

    protected function buildDbname()
    {
        $this->dbname = $this->builder->db->name;
    }

    protected function buildAuth()
    {
        $this->builder->buildAuth($this->query);
    }

    protected function buildMethod()
    {
        $this->method = $this->builder->buildMethod($this->query) ?: 'GET';
    }

    protected function buildUri()
    {
        $this->uri = $this->builder->buildUri($this->query);
    }

    protected function buildQueryParams()
    {
        $params = $this->builder->buildQueryParams($this->query);
        if (is_array($params)) {
            $params = http_build_query($params, '', '&');
        }
        if (!empty($params)) {
            $this->uri .= '?' . $params;
        }
    }

    protected function buildHeaders()
    {
        $this->headers = $this->builder->buildHeaders($this->query);
    }

    protected function buildBody()
    {
        $this->body = $this->builder->buildBody($this->query);
    }

    protected function buildFormParams()
    {
        $this->setFormParams($this->builder->buildFormParams($this->query));
    }

    protected function setFormParams($params)
    {
        if (!empty($params)) {
            $this->body = is_array($params) ? http_build_query($params, '', '&') : $params;
            $this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }
    }

    protected function buildProtocolVersion()
    {
        $this->version = $this->builder->buildProtocolVersion($this->query) ?: '1.1';
    }

    public function serialize()
    {
        $this->getWorker();
        $data = [];
        foreach (['dbname', 'method', 'uri', 'headers', 'body', 'version'] as $key) {
            $data[$key] = $this->{$key};
        }

        return serialize($data);
    }

    public function unserialize($string)
    {
        foreach (unserialize($string) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function isRaw()
    {
        return !empty($this->query->options['raw']);
    }
}
