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

use yii\db\QueryInterface;

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
class Query extends \yii\db\Query implements QueryInterface
{
    /**
     * @var string action that this query performs
     */
    public $action;

    /**
     * @var array query options e.g. raw, batch
     */
    public $options = [];

    public $count;

    public $body = [];

    public static function instantiate($action, $from, array $options = [])
    {
        $query = new static();

        return $query->action($action)->from($from)->options($options);
    }

    /**
     * @param null $db
     * @throws \Exception
     * @return Command
     */
    public function createCommand($db = null)
    {
        if ($db === null) {
            throw new \Exception('no db given to Query::createCommand');
        }

        $commandConfig = $db->getQueryBuilder()->build($this);

        return $db->createCommand($commandConfig);
    }

    public function one($db = null)
    {
        return $this->searchOne($db);
    }

    public function searchOne($db = null)
    {
        return $this->limit(1)->addOption('batch', false)->search();
    }

    public function all($db = null)
    {
        $rows = $this->searchAll();

        if (!empty($rows) && $this->indexBy !== null) {
            $result = [];
            foreach ($rows as $row) {
                if ($this->indexBy instanceof \Closure) {
                    $key = call_user_func($this->indexBy, $row);
                } else {
                    $key = $row[$this->indexBy];
                }
                $result[$key] = $row;
            }
            $rows = $result;
        }

        return $rows;
    }

    public function searchAll($db = null)
    {
        return $this->addOption('batch', true)->search();
    }

    public function search($db = null)
    {
        return $this->createCommand($db)->search();
    }

    public function delete($db = null, $options = [])
    {
        return $this->createCommand($db)->deleteByQuery($options);
    }

    public function count($q = '*', $db = null)
    {
        $this->count = $q;

        return (int) $this->searchAll();
    }

    public function exists($db = null)
    {
        return !empty(self::one($db));
    }

    public function action($action)
    {
        $this->action = $action;

        return $this;
    }

    public function addAction($action)
    {
        if (empty($this->action)) {
            $this->action = $action;
        }

        return $this;
    }

    public function addOption($name, $value)
    {
        if (!isset($this->options[$name])) {
            $this->options[$name] = $value;
        }

        return $this;
    }

    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    public function options($options)
    {
        $this->options = $options;

        return $this;
    }

    public function addOptions($options)
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }

        return $this;
    }

    public function body($body)
    {
        $this->body = $body;

        return $this;
    }

    public function innerJoin($table, $on = '', $params = [])
    {
        $this->join[] = (array) $table;

        return $this;
    }

    public function fields($fields)
    {
        if (is_array($fields) || $fields === null) {
            $this->fields = $fields;
        } else {
            $this->fields = func_get_args();
        }

        return $this;
    }

    public function source($source)
    {
        if (is_array($source) || $source === null) {
            $this->source = $source;
        } else {
            $this->source = func_get_args();
        }

        return $this;
    }
}
