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
use yii\db\QueryTrait;
use yii\helpers\ArrayHelper;

/**
 * Query represents API request in a way that is independent from concrete API.
 * Holds API request information:
 * - data passed into query:
 *      - action: action to be performed with this query, e.g. search, insert, update, delete
 *      - options: other additional options
 *      - select: fields to select
 *      - from: entity being queried, e.g. user
 *      - join: data how to join with other entities
 *      - other standard request options provided with QueryTrait: limit, offset, orderBy, ...
 * - data build with QueryBuilder:
 *      - HTTP request data: method, url, raw
 *          - in question: queryVars, body ????
 *      - parts: [key => value] combined data of request to be passed as GET or POST variables
 */
class Query extends Component implements QueryInterface
{
    use QueryTrait;

    public $db;

    /**
     * @var string action that this query performs
     */
    public $action;

    /**
     * @var array options for search
     */
    public $options = [];

    public $select;
    public $from;
    public $join;
    public $parts;

    /**
     * @var string request method e.g. POST
     */
    public $method;

    /**
     * @var string request url, without site
     */
    public $url;

    /**
     * @var array request query vars (GET parameters)
     */
    public $queryVars;

    /**
     * @var string request body vars (POST parameters)
     */
    public $body;

    /**
     * @var bool do not decode request
     */
    public $raw = false;

    /// DEPRECATED
    public $index;
    public $type;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        // setting the default limit according to api defaults
        if ($this->limit === null) {
            $this->limit = 'ALL';
        }
    }

    public function createCommand($db)
    {
        if ($db === null) {
            throw new \Exception('no db given to Query::createCommand');
            $db = Yii::$app->get('hiart');
        }

        $commandConfig = $db->getQueryBuilder()->build($this);

        return $db->createCommand($commandConfig);
    }

    public function join($type)
    {
        $this->join[] = (array) $type;

        return $this;
    }

    public function all($db = null)
    {
        $result = $this->createCommand($db)->search();
        if (empty($result['hits']['hits'])) {
            return [];
        }
        $rows = $result['hits']['hits'];
        if ($this->indexBy === null) {
            return $rows;
        }
        $models = [];
        foreach ($rows as $key => $row) {
            if ($this->indexBy !== null) {
                if (is_string($this->indexBy)) {
                    $key = isset($row['fields'][$this->indexBy]) ? reset($row['fields'][$this->indexBy]) : $row['_source'][$this->indexBy];
                } else {
                    $key = call_user_func($this->indexBy, $row);
                }
            }
            $models[$key] = $row;
        }

        return $models;
    }

    public function one($db = null)
    {
        $result = $this->createCommand($db)->search(['limit' => 1]);
        if (empty($result)) {
            return false;
        }
        $record = reset($result);

        return $record;
    }

    public function search($db = null, $options = [])
    {
        $result = $this->createCommand($db)->search($options);
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

    public function scalar($field, $db = null)
    {
        $record = self::one($db);
        if ($record !== false) {
            if ($field === '_id') {
                return $record['_id'];
            } elseif (isset($record['_source'][$field])) {
                return $record['_source'][$field];
            } elseif (isset($record['fields'][$field])) {
                return count($record['fields'][$field]) === 1 ? reset($record['fields'][$field]) : $record['fields'][$field];
            }
        }

        return null;
    }

    public function column($field, $db = null)
    {
        $command                        = $this->createCommand($db);
        $command->queryParts['_source'] = [$field];
        $result                         = $command->search();
        if (empty($result['hits']['hits'])) {
            return [];
        }
        $column = [];
        foreach ($result['hits']['hits'] as $row) {
            if (isset($row['fields'][$field])) {
                $column[] = $row['fields'][$field];
            } elseif (isset($row['_source'][$field])) {
                $column[] = $row['_source'][$field];
            } else {
                $column[] = null;
            }
        }

        return $column;
    }

    public function count($q = '*', $db = null)
    {
        $options          = [];
        $options['count'] = 1;

        return $this->createCommand($db)->search($options);
    }

    public function exists($db = null)
    {
        return self::one($db) !== false;
    }

    public function stats($groups)
    {
        $this->stats = $groups;

        return $this;
    }

    public function highlight($highlight)
    {
        $this->highlight = $highlight;

        return $this;
    }

    public function addAggregation($name, $type, $options)
    {
        $this->aggregations[$name] = [$type => $options];

        return $this;
    }

    public function addAgg($name, $type, $options)
    {
        return $this->addAggregation($name, $type, $options);
    }

    public function addSuggester($name, $definition)
    {
        $this->suggest[$name] = $definition;

        return $this;
    }

    public function query($query)
    {
        $this->query = $query;

        return $this;
    }

    public function filter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    public function from($from)
    {
        $this->from = $from;

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

    public function timeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getParts()
    {
        return $this->parts;
    }

    public function setPart($name, $value)
    {
        $this->parts[$name] = $value;
    }

    public function addParts($values)
    {
        $this->parts = ArrayHelper::merge($this->parts, $values);
    }
}
