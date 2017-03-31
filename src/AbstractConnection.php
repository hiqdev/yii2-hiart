<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use Closure;
use hiqdev\hiart\stream\Request;
use Yii;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\helpers\Json;

/**
 * Abstract connection class.
 */
abstract class AbstractConnection extends Component implements ConnectionInterface
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

    public static $dbname = 'hiart';

    public $name = 'hiart';

    public $userAgent = 'HiArt/0.x';

    public $baseUri;

    /**
     * @var array transport config will be used in Request for handler or proxy request
     */
    public $config = [];

    /**
     * @var object request handler common for all requests of this connection
     */
    protected $_handler;

    /**
     * @var AbstractQueryBuilder the query builder for this connection
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

    /**
     * @param null $name
     * @return ConnectionInterface|AbstractConnection
     */
    public static function getDb($name = null, $class = ConnectionInterface::class)
    {
        if ($name) {
            return Yii::$app->get($name);
        }
        if (Yii::$container->hasSingleton($class)) {
            return Yii::$container->get($class);
        }

        return Yii::$app->get(static::$dbname);
    }

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
     * @return AbstractQueryBuilder the query builder for this connection
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
     * Disables auth and calls $closure
     *
     * @param Closure $closure
     * @return mixed
     */
    public function callWithDisabledAuth(Closure $closure)
    {
        if ($this->isDisabledAuth()) {
            return call_user_func($closure);
        }

        try {
            $this->disableAuth();
            return call_user_func($closure);
        } finally {
            $this->enableAuth();
        }
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
     * Checks response method and raises exception if error found.
     * @param ResponseInterface $response response data from API
     * @throws ResponseErrorException when response is invalid
     */
    public function checkResponse(ResponseInterface $response)
    {
        if (isset($this->_errorChecker)) {
            $error = call_user_func($this->_errorChecker, $response);
        } else {
            $error = $this->getResponseError($response);
        }

        if ($error) {
            throw new ResponseErrorException($error, $response);
        }
    }

    /**
     * Method checks whether the response is an error.
     *
     * @param ResponseInterface $response
     * @return false|string the error text or boolean `false`, when the response is not an error
     */
    abstract public function getResponseError(ResponseInterface $response);

    protected $baseUriChecked;

    /**
     * Return API base uri.
     * Adds trailing slash if uri is domain only.
     * @return string
     */
    public function getBaseUri()
    {
        if (empty($this->baseUriChecked)) {
            if (preg_match('#^https?://[^/]+$#', $this->baseUri)) {
                $this->baseUri .= '/';
            }
            $this->baseUriChecked = true;
        }

        return $this->baseUri;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }
}
