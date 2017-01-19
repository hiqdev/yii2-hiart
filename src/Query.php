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

use Yii;
use yii\base\Component;
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
 *      - where, limit, offset, orderBy, indexBy
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
        $query = new static;

        return $query->action($action)->from($from)->options($options);
    }

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
        $result = $this->limit(1)->createCommand($db)->search();
        if (empty($result)) {
            return false;
        }
        $record = reset($result);

        return $record;
    }

    public function search($db = null)
    {
        $result = $this->createCommand($db)->search();

        if (!empty($result) && $this->indexBy !== null) {
            $rows = [];
            foreach ($result as $key => $row) {
                if (is_string($this->indexBy)) {
                    $key = isset($row['fields'][$this->indexBy]) ? $row['fields'][$this->indexBy] : $row['_source'][$this->indexBy];
                } else {
                    $key = call_user_func($this->indexBy, $row);
                }
                $rows[$key] = $row;
            }
            $result = $rows;
        }

        return $result;
    }

    public function delete($db = null, $options = [])
    {
        return $this->createCommand($db)->deleteByQuery($options);
    }

    public function count($q = '*', $db = null)
    {
        $this->count = $q;

        return (int) $this->createCommand($db)->search();
    }

    public function exists($db = null)
    {
        return self::one($db) !== false;
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

    public function addBatch($batch)
    {
        if (!isset($this->options['batch'])) {
            $this->options['batch'] = $batch;
        }

        return $this;
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
