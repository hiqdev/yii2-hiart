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

use Yii;
use yii\base\Component;
use yii\caching\Cache;
use yii\caching\Dependency;
use yii\httpclient\Request;

/**
 * The Command class implements execution of request.
 */
class Command extends Component
{
    /**
     * @var Connection
     */
    public $db;

    /**
     * @var Request request object
     */
    protected $request;

    /**
     * @var int the default number of seconds that query results can remain valid in cache.
     * Use 0 to indicate that the cached data will never expire. And use a negative number to indicate
     * query cache should not be used.
     * @see cache()
     */
    public $queryCacheDuration;

    /**
     * @var Dependency the dependency to be associated with the cached query result for this command
     * @see cache()
     */
    public $queryCacheDependency;

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public function queryInternal($default = null)
    {
        $rawRequest = $this->request->toString();

        $info = $this->db->getQueryCacheInfo($this->queryCacheDuration, $this->queryCacheDependency);
        if (is_array($info)) {
            /* @var $cache Cache */
            $cache = $info[0];
            $cacheKey = [
                __CLASS__,
                $this->db->baseUrl,
                $rawRequest,
            ];
            $result = $cache->get($cacheKey);
            if (is_array($result) && isset($result[0])) {
                Yii::trace("Query result served from cache:\n$rawRequest", __METHOD__);
                return $result[0];
            }
        }

        Yii::beginProfile($rawRequest, __METHOD__);

        $response = $this->db->send($this->request);

        Yii::endProfile($rawRequest, __METHOD__);

        if ($response->isOk) {
            $result = $response->getData();
            if (isset($cache, $cacheKey, $info)) {
                $cache->set($cacheKey, [$result], $info[1], $info[2]);
                Yii::trace('Saved query result in cache', __METHOD__);
            }

            return $result;
        }

        return $default;
    }

    public function queryOne()
    {
        return $this->queryInternal(null);
    }

    public function queryAll()
    {
        return $this->queryInternal([]);
    }

    public function count()
    {
        return $this->queryInternal(0);
    }

    /**
     * Enables query cache for this command.
     * @param int $duration the number of seconds that query result of this command can remain valid in the cache.
     * If this is not set, the value of [[Connection::queryCacheDuration]] will be used instead.
     * Use 0 to indicate that the cached data will never expire.
     * @param Dependency $dependency the cache dependency associated with the cached query result
     * @return $this the command object itself
     */
    public function cache($duration = null, $dependency = null)
    {
        $this->queryCacheDuration = $duration === null ? $this->db->queryCacheDuration : $duration;
        $this->queryCacheDependency = $dependency;
        return $this;
    }

    /**
     * Disables query cache for this command.
     * @return $this the command object itself
     */
    public function noCache()
    {
        $this->queryCacheDuration = -1;
        return $this;
    }
}
