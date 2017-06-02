<?php
/**
 * ActiveRecord for API.
 *
 * @see      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use hiqdev\hiart\librariesio\ConnectionInterface;
use Throwable;
use Yii;
use yii\caching\Cache;
use yii\caching\Dependency;
use yii\httpclient\Client;
use yii\httpclient\Request;

/**
 * Abstract connection class.
 */
class Connection extends Client
{
    /**
     * @var string to be specified in concrete implementation
     */
    public $queryBuilderClass = QueryBuilder::class;
    public $commandClass = Command::class;
    public $queryClass = Query::class;
    public $activeQueryClass = ActiveQuery::class;
    public static $dbname = 'hiart';
    public $requestConfig = [
        'headers' => [
            'User-Agent' => 'HiArt/0.x',
        ],
    ];

    /**
     * @var bool whether to enable query caching.
     * Note that in order to enable query caching, a valid cache component as specified
     * by [[queryCache]] must be enabled and [[enableQueryCache]] must be set true.
     * Also, only the results of the queries enclosed within [[cache()]] will be cached.
     * @see queryCache
     * @see cache()
     * @see noCache()
     */
    public $enableQueryCache = true;

    /**
     * @var int the default number of seconds that query results can remain valid in cache.
     * Defaults to 3600, meaning 3600 seconds, or one hour. Use 0 to indicate that the cached data will never expire.
     * The value of this property will be used when [[cache()]] is called without a cache duration.
     * @see enableQueryCache
     * @see cache()
     */
    public $queryCacheDuration = 3600;

    /**
     * @var Cache|string the cache object or the ID of the cache application component
     * that is used for query caching
     * @see enableQueryCache
     */
    public $queryCache = 'cache';

    /**
     * @var AbstractQueryBuilder the query builder for this connection
     */
    protected $_builder;

    /**
     * @var array query cache parameters for the [[cache()]] calls
     */
    private $_queryCacheInfo = [];

    /**
     * @param null $name
     * @return ConnectionInterface|AbstractConnection
     */
    public static function getDb($name = null, $class = self::class)
    {
        if ($name) {
            return Yii::$app->get($name);
        }
        if (Yii::$container->hasSingleton($class)) {
            return Yii::$container->get($class);
        }

        return Yii::$app->get(static::$dbname);
    }

    /**
     * Creates a command for execution.
     * @param Request $request the configuration for the Command class
     * @return Command the DB command
     */
    public function createCommand($request)
    {
        return new $this->commandClass([
            'db'      => $this,
            'request' => $request,
        ]);
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
     * Uses query cache for the queries performed with the callable.
     * When query caching is enabled ([[enableQueryCache]] is true and [[queryCache]] refers to a valid cache),
     * queries performed within the callable will be cached and their results will be fetched from cache if available.
     * For example,.
     *
     * ```php
     * // The customer will be fetched from cache if available.
     * // If not, the query will be made against DB and cached for use next time.
     * $customer = $db->cache(function (Connection $db) {
     *     return $db->createCommand('SELECT * FROM customer WHERE id=1')->queryOne();
     * });
     * ```
     *
     * Note that query cache is only meaningful for queries that return results. For queries performed with
     * [[Command::execute()]], query cache will not be used.
     *
     * @param callable $callable a PHP callable that contains DB queries which will make use of query cache.
     * The signature of the callable is `function (Connection $db)`.
     * @param int $duration the number of seconds that query results can remain valid in the cache. If this is
     * not set, the value of [[queryCacheDuration]] will be used instead.
     * Use 0 to indicate that the cached data will never expire.
     * @param Dependency $dependency the cache dependency associated with the cached query results
     * @throws \Exception|\Throwable if there is any exception during query
     * @return mixed the return result of the callable
     * @see enableQueryCache
     * @see queryCache
     * @see noCache()
     */
    public function cache(callable $callable, $duration = null, $dependency = null)
    {
        $this->_queryCacheInfo[] = [$duration === null ? $this->queryCacheDuration : $duration, $dependency];
        try {
            $result = call_user_func($callable, $this);
            array_pop($this->_queryCacheInfo);
            return $result;
        } catch (\Exception $e) {
            array_pop($this->_queryCacheInfo);
            throw $e;
        } catch (Throwable $e) {
            array_pop($this->_queryCacheInfo);
            throw $e;
        }
    }

    /**
     * Disables query cache temporarily.
     * Queries performed within the callable will not use query cache at all. For example,.
     *
     * ```php
     * $db->cache(function (Connection $db) {
     *
     *     // ... queries that use query cache ...
     *
     *     return $db->noCache(function (Connection $db) {
     *         // this query will not use query cache
     *         return $db->createCommand('SELECT * FROM customer WHERE id=1')->queryOne();
     *     });
     * });
     * ```
     *
     * @param callable $callable a PHP callable that contains DB queries which should not use query cache.
     * The signature of the callable is `function (Connection $db)`.
     * @throws \Exception|\Throwable if there is any exception during query
     * @return mixed the return result of the callable
     * @see enableQueryCache
     * @see queryCache
     * @see cache()
     */
    public function noCache(callable $callable)
    {
        $this->_queryCacheInfo[] = false;
        try {
            $result = call_user_func($callable, $this);
            array_pop($this->_queryCacheInfo);
            return $result;
        } catch (\Exception $e) {
            array_pop($this->_queryCacheInfo);
            throw $e;
        } catch (Throwable $e) {
            array_pop($this->_queryCacheInfo);
            throw $e;
        }
    }

    /**
     * Returns the current query cache information.
     * This method is used internally by [[Command]].
     * @param int $duration the preferred caching duration. If null, it will be ignored.
     * @param Dependency $dependency the preferred caching dependency. If null, it will be ignored.
     * @return array the current query cache information, or null if query cache is not enabled
     * @internal
     */
    public function getQueryCacheInfo($duration, $dependency)
    {
        if (!$this->enableQueryCache) {
            return null;
        }

        $info = end($this->_queryCacheInfo);
        if (is_array($info)) {
            if ($duration === null) {
                $duration = $info[0];
            }
            if ($dependency === null) {
                $dependency = $info[1];
            }
        }

        if ($duration === 0 || $duration > 0) {
            if (is_string($this->queryCache) && Yii::$app) {
                $cache = Yii::$app->get($this->queryCache, false);
            } else {
                $cache = $this->queryCache;
            }
            if ($cache instanceof Cache) {
                return [$cache, $duration, $dependency];
            }
        }

        return null;
    }
}
