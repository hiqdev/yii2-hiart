<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

abstract class AbstractRequest implements RequestInterface
{
    /**
     * @var string response implementation to be specified in concrete implementation
     */
    protected $responseClass;

    /**
     * @var string request handler implementation to be specified in concrete implementation
     */
    protected $handlerClass;

    /**
     * @var QueryBuilderInterface
     */
    protected $builder;

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

    protected $isBuilt;
    protected array $parts = [];
    protected $fullUri;

    abstract public function send($options = []);

    public function __construct(QueryBuilderInterface $builder, Query $query)
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

    public function getFullUri()
    {
        if ($this->fullUri === null) {
            $this->fullUri = $this->createFullUri();
        }

        return $this->fullUri;
    }

    public function createFullUri()
    {
        return ($this->isFullUri($this->uri) ? '' : $this->getDb()->getBaseUri()) . $this->uri;
    }

    public function isFullUri($uri)
    {
        return preg_match('/^https?:\\/\\//i', (string)$uri);
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

    public function build()
    {
        if ($this->isBuilt === null) {
            if (!empty($this->query)) {
                $this->updateFromQuery();
            }
            $this->isBuilt = true;
        }
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

    protected function buildDbname()
    {
        $this->dbname = $this->getDb()->name;
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
        if (empty($this->headers['User-Agent'])) {
            $this->headers['User-Agent'] = $this->prepareUserAgent();
        }
        $this->headers['traceparent'] = $this->getDb()->getTraceparentHeader();
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

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __serialize(): array
    {
        return $this->getParts();
    }

    public function unserialize(string $serialized): void
    {
        $this->__unserialize(unserialize($serialized));
    }

    public function __unserialize(array $serialized): void
    {
        foreach ($serialized as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getParts(): array
    {
        if (empty($this->parts)) {
            $this->build();
            foreach (['dbname', 'method', 'uri', 'headers', 'body', 'version'] as $key) {
                $this->parts[$key] = $this->{$key};
            }
        }

        return $this->parts;
    }

    public function isRaw()
    {
        return !empty($this->query->options['raw']);
    }

    public function getHandler()
    {
        $handler = $this->getDb()->getHandler();
        if ($handler === null) {
            $handler = $this->createHandler();
        }

        return $handler;
    }

    protected function createHandler()
    {
        $config = $this->prepareHandlerConfig($this->getDb()->config);

        return new $this->handlerClass($config);
    }

    protected function prepareHandlerConfig($config)
    {
        return $config;
    }

    protected function prepareUserAgent()
    {
        return $this->getDb()->getUserAgent();
    }

    /**
     * @return AbstractConnection|ConnectionInterface
     */
    public function getDb()
    {
        return isset($this->builder) ? $this->builder->db : AbstractConnection::getDb($this->dbname);
    }

    /**
     * @param string $header
     * @param string $value
     * @return $this
     */
    public function addHeader($header, $value)
    {
        if (!isset($this->headers[$header])) {
            $this->headers[$header] = [];
        }

        $this->headers[$header][] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     * Should be declared abstract, but it is not supported in PHP5.
     */
    public static function isSupported()
    {
        return false;
    }
}
