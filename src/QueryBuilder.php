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

use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\httpclient\Request;

/**
 * Abstract QueryBuilder.
 *
 * QueryBuilder builds a request from the specification given as a [[Query]] object.
 */
class QueryBuilder extends Object
{
    /**
     * @var Connection
     */
    public $db;

    public function __construct($connection, $config = [])
    {
        $this->db = $connection;
        parent::__construct($config);
    }

    /**
     * @param array $params
     * @return Request
     */
    public function createRequest($params = [])
    {
        return $this->db->createRequest();
    }

    /**
     * Builds config array to create Command.
     * @param Query $query
     * @throws NotSupportedException
     * @return Request
     */
    public function build(Query $query, $params = [], array $options = [])
    {
        $query = $query->prepare($this);
        $params = empty($params) ? $query->params : array_merge($params, $query->params);

        $request = $this->createRequest($params);

        $this->buildAuth($request, $params);
        $this->buildMethod($request, 'select', $params);
        $this->buildFrom($request, $query->from, $params);
        $this->buildWhere($request, $query->where, $params);

        $this->buildCount($request, $query->count, $params);
        $this->buildOrderBy($request, $query->orderBy, $params);
        $this->buildLimit($request, $query->limit, $query->offset, $params);

        $request->setOptions($options);

        return $request;
    }

    public function buildAuth(Request $request, $params = [])
    {
        return $request;
    }

    public function buildMethod(Request $request, $action, $params = [])
    {
        static $defaultMethods = [
            'delete' => 'DELETE',
            'get'    => 'GET',
            'head'   => 'HEAD',
            'insert' => 'POST',
            'post'   => 'GET',
            'put'    => 'PUT',
            'search' => 'GET',
            'select' => 'GET',
            'update' => 'PUT',
        ];

        $method = isset($defaultMethods[$action]) ? $defaultMethods[$action] : 'POST';
        return $request->setMethod($method);
    }

    public function buildFrom(Request $request, $from, $params = [])
    {
        return $request->setUrl($from);
    }

    public function buildWhere(Request $request, $condition, $params = [])
    {
        $where = $this->buildCondition($condition, $params);

        if ($where) {
            $request->addData($where);
        }

        return $request;
    }

    public function buildCount(Request $request, $count, $params = [])
    {
        if ($count) {
            $request->on(Request::EVENT_AFTER_SEND, function (\yii\httpclient\RequestEvent $event) {
                $data = $event->response->getData();
                $event->response->setData(count($data));
            });
        }

        return $request;
    }

    public function buildOrderBy(Request $request, $orderBy, $params = [])
    {
        return $request;
    }

    public function buildLimit(Request $request, $limit, $offset = 0, $params = [])
    {
        return $request;
    }

    /**
     * Creates insert request.
     * @param string $url
     * @param array $columns
     * @param array $options
     * @return AbstractTransport
     */
    public function insert($url, $columns, &$params = [], array $options = [])
    {
        $request = $this->createRequest();

        $this->buildMethod($request, 'insert', $params);
        $this->buildFrom($request, url, $params);

        $request->setData($columns);
        $request->setOptions($options);

        return $request;
    }

    /**
     * Creates update request.
     * @param string $url
     * @param array $columns
     * @param array $condition
     * @param array $options
     * @return AbstractTransport
     */
    public function update($url, $columns, $condition = [], &$params = [], array $options = [])
    {
        $request = $this->createRequest();
        $this->buildMethod($request, 'update', $params);
        $this->buildFrom($request, url, $params);

        $this->buildWhere($request, $condition, $params);

        $request->addData($columns);
        $request->setOptions($options);

        return $request;
    }

    /**
     * Creates delete request.
     * @param string $table
     * @param array $condition
     * @param array $options
     * @return AbstractTransport
     */
    public function delete($table, $condition = [], &$params = [], array $options = [])
    {
        $request = $this->createRequest();
        $this->buildMethod($request, 'update', $params);
        $this->buildFrom($request, url, $params);
        $this->buildWhere($request, $condition, $params);

        $request->setOptions($options);

        return $request;
    }

    /**
     * Creates request for given action.
     * @param string $action
     * @param string $table
     * @param mixed $body
     * @param array $options
     * @return AbstractTransport
     */
    public function perform($action, $table, $body, $options = [])
    {
        $query = $this->createQuery($action, $table, $options)->body($body);

        return $this->createRequest($query);
    }

    public function buildCondition($condition)
    {
        static $builders = [
            'and'     => 'buildAndCondition',
            'between' => 'buildBetweenCondition',
            'eq'      => 'buildEqCondition',
            'ne'      => 'buildNotEqCondition',
            'in'      => 'buildInCondition',
            'ni'      => 'buildNotInCondition',
            'like'    => 'buildLikeCondition',
            'ilike'   => 'buildIlikeCondition',
            'gt'      => 'buildCompareCondition',
            'ge'      => 'buildCompareCondition',
            'lt'      => 'buildCompareCondition',
            'le'      => 'buildCompareCondition',
        ];
        if (empty($condition)) {
            return [];
        }
        if (!is_array($condition)) {
            throw new NotSupportedException('String conditions in where() are not supported by HiArt.');
        }

        if (isset($condition[0])) { // operator format: operator, operand 1, operand 2, ...
            $operator = strtolower($condition[0]);
            if (isset($builders[$operator])) {
                $method = $builders[$operator];
                array_shift($condition); // Shift build condition

                return $this->$method($operator, $condition);
            } else {
                throw new InvalidParamException('Found unknown operator in query: ' . $operator);
            }
        } else {
            return $this->buildHashCondition($condition);
        }
    }

    protected function buildHashCondition($condition)
    {
        $parts = [];
        foreach ($condition as $attribute => $value) {
            if (is_array($value)) { // IN condition
                // $parts[] = [$attribute.'s' => join(',',$value)];
                $parts[$attribute . 's'] = implode(',', $value);
            } else {
                $parts[$attribute] = $value;
            }
        }

        return $parts;
    }

    protected function buildLikeCondition($operator, $operands)
    {
        return [$operands[0] . '_like' => $operands[1]];
    }

    protected function buildIlikeCondition($operator, $operands)
    {
        return [$operands[0] . '_ilike' => $operands[1]];
    }

    protected function buildCompareCondition($operator, $operands)
    {
        if (!isset($operands[0], $operands[1])) {
            throw new InvalidParamException("Operator '$operator' requires three operands.");
        }

        return [$operands[0] . '_' . $operator => $operands[1]];
    }

    protected function buildAndCondition($operator, $operands)
    {
        $parts = [];
        foreach ($operands as $operand) {
            if (is_array($operand)) {
                $parts = ArrayHelper::merge($this->buildCondition($operand), $parts);
            }
        }
        if (!empty($parts)) {
            return $parts;
        } else {
            return [];
        }
    }

    protected function buildBetweenCondition($operator, $operands)
    {
        throw new NotSupportedException('Between condition is not supported by HiArt.');
    }

    protected function buildInCondition($operator, $operands, $not = false)
    {
        if (!isset($operands[0], $operands[1])) {
            throw new InvalidParamException("Operator '$operator' requires two operands.");
        }

        list($column, $values) = $operands;

        if (count($column) > 1) {
            return $this->buildCompositeInCondition($operator, $column, $values);
        } elseif (is_array($column)) {
            $column = reset($column);
        }

        foreach ((array) $values as $i => $value) {
            if (is_array($value)) {
                $values[$i] = $value = isset($value[$column]) ? $value[$column] : null;
            }
            if ($value === null) {
                unset($values[$i]);
            }
        }

        if ($not) {
            $key = $column . '_ni'; // not in
        } else {
            $key = $column . '_in';
        }
        return [$key => $values];
    }

    protected function buildNotInCondition($operator, $operands)
    {
        return $this->buildInCondition($operator, $operands, true);
    }

    protected function buildEqCondition($operator, $operands)
    {
        $key = array_shift($operands);

        return [$key => reset($operands)];
    }

    protected function buildNotEqCondition($operator, $operands)
    {
        $key = array_shift($operands);

        return [$key . '_' . $operator => reset($operands)];
    }

    protected function buildCompositeInCondition($operator, $columns, $values)
    {
        throw new NotSupportedException('composite in is not supported by HiArt.');
    }
}
