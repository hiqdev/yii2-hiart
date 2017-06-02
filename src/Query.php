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

use Exception;
use yii\caching\Dependency;
use yii\db\Query as BaseQuery;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;

/**
 * Query represents API query in a way that is independent from a concrete API.
 * Holds API query information:
 * - general query data
 *      - action: action to be performed with this query, e.g. search, insert, update, delete
 *      - options: other additional options, like
 *          - raw: do not decode response
 *          - batch: batch(bulk) request
 *          - timeout, ...
 * - insert/update query data
 *      - body: insert or update data
 * - select query data
 *      - select: fields to select
 *      - count: marks count query
 *      - from: entity being queried, e.g. user
 *      - join: data how to join with other entities
 * - other standard query options provided with QueryTrait:
 *      - where, limit, offset, orderBy, indexBy.
 */
class Query extends BaseQuery implements QueryInterface
{
    /**
     * @var string action that this query performs
     */
    public $action;

    /**
     * @var array query options e.g. raw, batch
     */
    public $options = [];

    /**
     * @var string the COUNT expression
     */
    public $count;

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

    /**
     * @param null|Connection $db
     * @throws Exception
     * @return Command
     */
    public function createCommand($db = null)
    {
        if ($db === null) {
            throw new Exception('no db given to Query::createCommand');
        }

        $request = $db->getQueryBuilder()->build($this);

        $command = $db->createCommand($request);

        if ($this->queryCacheDuration !== null) {
            $command->cache($this->queryCacheDuration, $this->queryCacheDependency);
        }

        return $command;
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
        $this->queryCacheDuration = $duration;
        $this->queryCacheDependency = $dependency;
        return $this;
    }

    public function one($db = null)
    {
        $this->limit(1);

        $row = parent::one($db);
        if (ArrayHelper::isIndexed($row)) {
            return reset($row);
        }

        return $row;
    }

    public function count($q = '*', $db = null)
    {
        $this->count = $q;
        return $this->createCommand($db)->count();
    }
}
