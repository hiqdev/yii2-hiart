<?php

/*
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use Closure;
use GuzzleHttp\Client as Handler;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\helpers\Json;

/**
 * Connection class.
 *
 * Example configuration:
 * ```php
 * 'components' => [
 *     'hiart' => [
 *         'class' => 'hiqdev\hiart\Connection',
 *         'config' => [
 *             'base_uri' => 'https://api.site.com/',
 *         ],
 *     ],
 * ],
 * ```
 */
class Connection extends Component
{
    const EVENT_AFTER_OPEN = 'afterOpen';

    /**
     * @var array Config
     */
    public $config = [];

    /**
     * @var Handler
     */
    protected static $_handler = null;

    /**
     * @var array authorization config
     */
    protected $_auth = [];

    /**
     * @var Closure Callback to test if API response has error
     * The function signature: `function ($response)`
     * Must return `null`, if the response does not contain an error.
     */
    protected $_errorChecker;

    public function setAuth($auth)
    {
        $this->_auth = $auth;
    }

    public function getAuth()
    {
        if ($this->_auth instanceof Closure) {
            $this->_auth = call_user_func($this->_auth, $this);
        }

        return $this->_auth;
    }

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->config['base_uri']) {
            throw new InvalidConfigException('The `base_uri` config option must be set');
        }
    }

    /**
     * Closes the connection when this component is being serialized.
     * @return array
     */
    public function __sleep()
    {
        return array_keys(get_object_vars($this));
    }

    /**
     * Returns the name of the DB driver for the current [[dsn]].
     *
     * @return string name of the DB driver
     */
    public function getDriverName()
    {
        return 'hiresource';
    }

    /**
     * Creates a command for execution.
     *
     * @param array $config the configuration for the Command class
     *
     * @return Command the DB command
     */
    public function createCommand($config = [])
    {
        $config['db'] = $this;
        $command      = new Command($config);

        return $command;
    }

    /**
     * Creates new query builder instance.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return new QueryBuilder($this);
    }

    /**
     * Performs GET HTTP request.
     * @param string $url   URL
     * @param array  $query query options
     * @param string $body  request body
     * @param bool   $raw   if response body contains JSON and should be decoded
     * @throws HiArtException
     * @throws \yii\base\InvalidConfigException
     * @return mixed response
     */
    public function get($url, $query = [], $body = null, $raw = false)
    {
        return $this->makeRequest('GET', $url, $query, $body, $raw);
    }

    /**
     * Performs HEAD HTTP request.
     * @param string $url   URL
     * @param array  $query query options
     * @param string $body  request body
     * @throws HiArtException
     * @throws \yii\base\InvalidConfigException
     * @return mixed response
     */
    public function head($url, $query = [], $body = null)
    {
        return $this->makeRequest('HEAD', $url, $query, $body, $raw);
    }

    /**
     * Performs POST HTTP request.
     * @param string $url   URL
     * @param array  $query query options
     * @param string $body  request body
     * @param bool   $raw   if response body contains JSON and should be decoded
     * @throws HiArtException
     * @throws \yii\base\InvalidConfigException
     * @return mixed response
     */
    public function post($url, $query = [], $body = null, $raw = false)
    {
        return $this->makeRequest('POST', $url, $query, $body, $raw);
    }

    /**
     * Performs PUT HTTP request.
     * @param string $url   URL
     * @param array  $query query options
     * @param string $body  request body
     * @param bool   $raw   if response body contains JSON and should be decoded
     * @throws HiArtException
     * @throws \yii\base\InvalidConfigException
     * @return mixed response
     */
    public function put($url, $query = [], $body = null, $raw = false)
    {
        return $this->makeRequest('PUT', $url, $query, $body, $raw);
    }

    /**
     * Performs DELETE HTTP request.
     * @param string $url   URL
     * @param array  $query query options
     * @param string $body  request body
     * @param bool   $raw   if response body contains JSON and should be decoded
     * @throws HiArtException
     * @throws \yii\base\InvalidConfigException
     * @return mixed response
     */
    public function delete($url, $query = [], $body = null, $raw = false)
    {
        return $this->makeRequest('DELETE', $url, $query, $body, $raw);
    }

    /**
     * XXX DEPRECATED in favour of post().
     * @param $url
     * @param array $query
     * @return mixed
     */
    public function perform($url, $body = [])
    {
        return $this->makeRequest('DELETE', $url, [], $body);
    }

    /**
     * Make request and check for error.
     * @param string $url   URL
     * @param array  $query query options, (GET parameters)
     * @param string $body  request body, (POST parameters)
     * @param bool   $raw   if response body contains JSON and should be decoded
     * @throws HiArtException
     * @throws \yii\base\InvalidConfigException
     * @return mixed response
     */
    public function makeRequest($method, $url, $query = [], $body = null, $raw = false)
    {
        $result = $this->handleRequest($method, $this->prepareUrl($url, $query), $body, $raw);

        return $this->checkResponse($result, $url, $query);
    }

    /**
     * Creates URL.
     * @param mixed $path path
     * @param array $query query options
     * @return array
     */
    private function prepareUrl($path, array $query = [])
    {
        $url = $path;
        $query = array_merge($this->getAuth(), $query);
        if (!empty($query)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($query);
        }

        return $url;
    }

    /**
     * Handles the request with handler.
     * Returns array or raw response content, if $raw is true.
     *
     * @param string $method POST, GET, etc
     * @param string $url the URL for request, not including proto and site
     * @param array|string $body the request body. When array - will be sent as POST params, otherwise - as RAW body.
     * @param bool $raw Whether to decode data, when response is decodeable (JSON).
     * @return array|string
     */
    protected function handleRequest($method, $url, $body = null, $raw = false)
    {
        $method  = strtoupper($method);
        $profile = $method . ' ' . $url . '#' . (is_array($body) ? http_build_query($body) : $body);
        $options = [(is_array($body) ? 'form_params' : 'body') => $body];
        Yii::beginProfile($profile, __METHOD__);
        $response = $this->getHandler()->request($method, $url, $options);
        Yii::endProfile($profile, __METHOD__);

        $res = $response->getBody()->getContents();
        if (!$raw && preg_grep('|application/json|i', $response->getHeader('Content-Type'))) {
            $res = Json::decode($res);
        }

        return $res;
    }

    /**
     * Returns the request handler (Guzzle client for the moment).
     * Creates and setups handler if not set.
     * @return Handler
     */
    public function getHandler()
    {
        if (static::$_handler === null) {
            static::$_handler = new Handler($this->config);
            static::$_handler->setUserAgent('hiart/0.x');
        }

        return static::$_handler;
    }

    /**
     * Set handler manually.
     * @param Handler $value
     * @return void
     */
    public function setHandler($value)
    {
        static::$_handler = $value;
    }

    /**
     * Try to decode error information if it is valid json, return it if not.
     * @param $body
     * @return mixed
     */
    protected function decodeErrorBody($body)
    {
        try {
            $decoded = Json::decode($body);
            if (isset($decoded['error'])) {
                $decoded['error'] = preg_replace('/\b\w+?Exception\[/',
                    "<span style=\"color: red;\">\\0</span>\n               ", $decoded['error']);
            }

            return $decoded;
        } catch (InvalidParamException $e) {
            return $body;
        }
    }

    /**
     * Setter for errorChecker.
     * @param Closure|array $value
     * @return void
     */
    public function setErrorChecker($value)
    {
        $this->_errorChecker = $value;
    }

    /**
     * Checks response with errorChecker callback and raises exception if error.
     * @param array  $response response data from API
     * @param string $url      request URL
     * @param array  $options  request data
     * @throws ErrorResponseException
     * @return array
     */
    protected function checkResponse($response, $url, $options)
    {
        if (isset($this->_errorChecker)) {
            $error = call_user_func($this->_errorChecker, $response);
            if ($error !== null) {
                throw new ErrorResponseException($error, [
                    'requestUrl' => $url,
                    'request'    => $options,
                    'response'   => $response,
                ]);
            }
        }

        return $response;
    }
}
