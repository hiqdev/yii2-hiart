<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use Closure;
use hiqdev\hiart\stream\Request;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\helpers\Json;

/**
 * Abstract connection class.
 */
abstract class AbstractConnection extends Component
{
    const EVENT_AFTER_OPEN = 'afterOpen';

    /**
     * @var string to be specified in concrete implementation
     */
    public $queryBuilderClass;

    public $requestClass = Request::class;

    public $commandClass = Command::class;

    public $queryClass = Query::class;

    public $activeQueryClass = ActiveQuery::class;

    public $name = 'hiart';

    /**
     * @var array connection config will be passed to handler
     */
    public $config = [];

    /**
     * @var object request handler common for all requests of this connection
     */
    protected $_handler;

    /**
     * @var QueryBuilder the query builder for this connection
     */
    protected $_builder;

    /**
     * @var array authorization config
     */
    protected $_auth = [];

    /**
     * @var bool is auth disabled
     */
    protected $_disabledAuth = false;

    /**
     * @var Closure callback to test if API response has error
     * The function signature: `function ($response)`
     * Must return `null`, if the response does not contain an error
     */
    protected $_errorChecker;

    public function setAuth($auth)
    {
        $this->_auth = $auth;
    }

    /**
     * Returns auth settings.
     * @return array
     */
    public function getAuth()
    {
        if ($this->_disabledAuth) {
            return [];
        }
        if ($this->_auth instanceof Closure) {
            $this->_auth = call_user_func($this->_auth, $this);
        }

        return $this->_auth;
    }

    public function disableAuth()
    {
        $this->_disabledAuth = true;
    }

    public function enableAuth()
    {
        $this->_disabledAuth = false;
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

        if (!isset($this->config['headers']['User-Agent'])) {
            $this->config['headers']['User-Agent'] = 'HiArt/0.x';
        }
    }

    public function getBaseUri()
    {
        return $this->config['base_uri'];
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
     * @return string name of the DB driver
     */
    public function getDriverName()
    {
        return 'hiart';
    }

    /**
     * Creates a command for execution.
     * @param array $config the configuration for the Command class
     * @return Command the DB command
     */
    public function createCommand(array $config = [])
    {
        $config['db'] = $this;

        return new $this->commandClass($config);
    }

    /**
     * @return QueryBuilder the query builder for this connection
     */
    public function getQueryBuilder()
    {
        if ($this->_builder === null) {
            $this->_builder = new $this->queryBuilderClass($this);
        }

        return $this->_builder;
    }

    /**
     * Handler is created and set by request.
     * @see setHandler
     * @return object
     */
    public function getHandler()
    {
        return $this->_handler;
    }

    /**
     * Requests use this function to keep request handler.
     * @param object $handler
     */
    public function setHandler($handler)
    {
        $this->_handler = $handler;
    }

    /**
     * Sends given request.
     * @param Request $request
     * @param array $options
     * @return Response
     */
    public function send(Request $request, array $options = [])
    {
        $worker = $this->getHandler()->send($request->getWorker(), $options);

        return new Response($worker, $request);
    }

    /**
     * @return boolean
     */
    public function isDisabledAuth()
    {
        return $this->_disabledAuth;
    }

    /**
     * @param boolean $disabledAuth
     */
    public function setDisabledAuth($disabledAuth)
    {
        $this->_disabledAuth = $disabledAuth;
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
     * @param Closure $checker
     */
    public function setErrorChecker($checker)
    {
        $this->_errorChecker = $checker;
    }

    /**
     * Checks response with checkError method and raises exception if error.
     * @param Response $response response data from API
     * @throws ErrorResponseException
     * @return mixed response data
     */
    public function checkResponse(Response $response)
    {
        $error = $this->checkError($response);
        if ($error) {
            throw new ErrorResponseException($error, [
                'request' => $response->getRequest()->getParts(),
                'response' => $response->getData(),
            ]);
        }

        return $response->getData();
    }

    /**
     * Checks response with errorChecker callback and returns error text if error.
     * @param Response $response
     * @return string|false error text or false
     */
    public function checkError(Response $response)
    {
        if (isset($this->_errorChecker)) {
            return call_user_func($this->_errorChecker, $response);
        } else {
            return $this->isError($response);
        }
    }

    /**
     * Default error checker. TODO check something in response?
     * @param Response $response
     * @return bool
     */
    public function isError(Response $response)
    {
        return false;
    }
}
