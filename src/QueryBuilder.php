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

use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;

/**
 * QueryBuilder builds an HiArt query based on the specification given as a [[Query]] object.
 */
class QueryBuilder extends \yii\base\Object
{
    private $_sort = [
        SORT_ASC  => '_asc',
        SORT_DESC => '_desc',
    ];

    public $db;

    public function __construct($connection, $config = [])
    {
        $this->db = $connection;
        parent::__construct($config);
    }

    /**
     * @param Query $query
     * @throws NotSupportedException
     * @return array
     */
    public function build($query)
    {
        $query->prepare();

        $this->buildSelect($query);
        $this->buildLimit($query);
        $this->buildPage($query);
        $this->buildOrderBy($query);
        $this->buildWhere($query);

        return ['query' => $query];
    }

    public function buildLimit(Query $query)
    {
        $limit = $query->limit;
        if (!empty($limit)) {
            if ($limit === -1) {
                $limit = 'ALL';
            }
            $query->setPart('limit', $limit);
        }
    }

    public function buildPage(Query $query)
    {
        if ($query->offset > 0) {
            $this->setPart('page', ceil($this->offset / $this->limit) + 1);
        }
    }

    public function buildOrderBy(Query $query)
    {
        $orderBy = $query->orderBy;
        if (!empty($orderBy)) {
            $this->setPart('orderby', key($orderBy) . $this->_sort[reset($orderBy)]);
        }
    }

    public function buildSelect(Query $query)
    {
        if (!empty($query->select)) {
            $select = [];
            foreach ($query->select as $name) {
                $select[$name] = $name;
            }
            $this->setPart('select', $select);
        }
    }

    public function buildWhere(Query $query)
    {
        $query->addParts($this->buildCondition($query->where));
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

    private function buildHashCondition($condition)
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

    private function buildLikeCondition($operator, $operands)
    {
        return [$operands[0] . '_like' => $operands[1]];
    }

    private function buildIlikeCondition($operator, $operands)
    {
        return [$operands[0] . '_ilike' => $operands[1]];
    }

    private function buildCompareCondition($operator, $operands)
    {
        if (!isset($operands[0], $operands[1])) {
            throw new InvalidParamException("Operator '$operator' requires three operands.");
        }

        return [$operands[0] . '_' . $operator => $operands[1]];
    }

    private function buildAndCondition($operator, $operands)
    {
        $parts = [];
        foreach ($operands as $operand) {
            if (is_array($operand)) {
                $parts = \yii\helpers\ArrayHelper::merge($this->buildCondition($operand), $parts);
            }
        }
        if (!empty($parts)) {
            return $parts;
        } else {
            return [];
        }
    }

    private function buildBetweenCondition($operator, $operands)
    {
        throw new NotSupportedException('Between condition is not supported by HiArt.');
    }

    private function buildInCondition($operator, $operands, $not = false)
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

    private function buildNotInCondition($operator, $operands)
    {
        return $this->buildInCondition($operator, $operands, true);
    }

    private function buildEqCondition($operator, $operands)
    {
        $key = array_shift($operands);

        return [$key => reset($operands)];
    }

    private function buildNotEqCondition($operator, $operands)
    {
        $key = array_shift($operands);

        return [$key . '_' . $operator => reset($operands)];
    }

    protected function buildCompositeInCondition($operator, $columns, $values)
    {
        throw new NotSupportedException('composite in is not supported by HiArt.');
    }
}
