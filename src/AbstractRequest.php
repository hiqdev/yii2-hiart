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

abstract class AbstractRequest implements \Serializable
{
    /**
     * @var string response implementation to be specified in concrete implementation.
     */
    protected $responseClass;

    /**
     * @var string transport implementation to be specified in concrete implementation.
     */
    public $handlerClass;

    protected $builder;

    /**
     * @var object
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

    protected $parts = [];

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

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return Worker
     */
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

    abstract protected function createWorker();

    public function send($options = [])
    {
        $handler = $this->builder->getHandler();
        $worker = $handler->send($this->getWorker(), $options);

        return new $this->responseClass($worker, $request);
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
        return serialize($this->getParts());
    }

    public function unserialize($string)
    {
        foreach (unserialize($string) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getParts()
    {
        if (empty($this->parts)) {
            $this->getWorker();
            foreach (['dbname', 'method', 'uri', 'headers', 'body', 'version'] as $key) {
                $this->parts[$key] = $this->{$key};
            }
        }

        return $this->parts;
    }

    public function send()
    {
    }

    public function isRaw()
    {
        return !empty($this->query->options['raw']);
    }
}
