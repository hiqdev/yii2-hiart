<?php
/**
 * @link http://hiqdev.com/yii2-hiar
 * @copyright Copyright (c) 2015 HiQDev
 * @license http://hiqdev.com/yii2-hiar/license
 */

namespace hiqdev\hiar;

use Yii;
use yii\base\Component;
use yii\db\QueryInterface;
use yii\db\QueryTrait;

class Query extends Component implements QueryInterface
{
    use QueryTrait;

    public $fields;

    public $source;

    public $index;

    public $type;

    public $timeout;

    public $query;

    public $filter;

    public $highlight;

    public $aggregations = [];

    public $stats = [];

    public $suggest = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // setting the default limit according to api defaults
        if ($this->limit === null) {
            $this->limit = 25;
        }
    }

    public function createCommand($db = null)
    {
        if ($db === null) {
            $db = Yii::$app->get('hiresource');
        }

        $commandConfig = $db->getQueryBuilder()->build($this);

        return $db->createCommand($commandConfig);
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
                return count($record['fields'][$field]) == 1 ? reset($record['fields'][$field]) : $record['fields'][$field];
            }
        }
        return null;
    }

    public function column($field, $db = null)
    {
        $command = $this->createCommand($db);
        $command->queryParts['_source'] = [$field];
        $result = $command->search();
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
        $options = [];
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

    public function from($index, $type = null)
    {
        $this->index = $index;
        $this->type = $type;
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
}