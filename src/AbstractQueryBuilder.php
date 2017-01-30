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

use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;

/**
 * Abstract QueryBuilder.
 *
 * QueryBuilder builds a request from the specification given as a [[Query]] object.
 */
abstract class AbstractQueryBuilder extends \yii\base\Object implements QueryBuilderInterface
{
    /**
     * @var AbstractConnection
     */
    public $db;

    public function __construct($connection, $config = [])
    {
        $this->db = $connection;
        parent::__construct($config);
    }

    /**
     * Builds config array to create Command.
     * @param Query $query
     * @throws NotSupportedException
     * @return array
     */
    public function build(Query $query)
    {
        return ['request' => $this->createRequest($query)];
    }

    public function createRequest($query)
    {
        return new $this->db->requestClass($this, $query);
    }

    /**
     * Prepares query before actual building.
     * This function for you to redefine.
     * It will be called before other build functions.
     * @param Query $query
     */
    public function prepare(Query $query)
    {
        return $query->prepare($this);
    }

    /**
     * This function is for you to provide your authentication.
     * @param Query $query
     */
    abstract public function buildAuth(Query $query);

    abstract public function buildMethod(Query $query);

    abstract public function buildUri(Query $query);

    abstract public function buildHeaders(Query $query);

    abstract public function buildProtocolVersion(Query $query);

    abstract public function buildQueryParams(Query $query);

    abstract public function buildFormParams(Query $query);

    abstract public function buildBody(Query $query);

    /**
     * Creates insert request.
     * @param string $table
     * @param array $columns
     * @param array $options
     * @return AbstractRequest
     */
    public function insert($table, $columns, array $options = [])
    {
        return $this->perform('insert', $table, $columns, $options);
    }

    /**
     * Creates update request.
     * @param string $table
     * @param array $columns
     * @param array $options
     * @return AbstractRequest
     */
    public function update($table, $columns, $condition = [], array $options = [])
    {
        $query = $this->createQuery('update', $table, $options)->body($columns)->where($condition);

        return $this->createRequest($query);
    }

    public function delete($table, $condition = [], array $options = [])
    {
        $query = $this->createQuery('delete', $table, $options)->where($condition);

        return $this->createRequest($query);
    }

    public function perform($action, $table, $body, $options = [])
    {
        $query = $this->createQuery($action, $table, $options)->body($body);

        return $this->createRequest($query);
    }

    public function createQuery($action, $table, array $options = [])
    {
        $class = $this->db->queryClass;

        return $class::instantiate($action, $table, $options);
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
