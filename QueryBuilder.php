<?php
/**
 * @link http://hiqdev.com/yii2-hiar
 * @copyright Copyright (c) 2015 HiQDev
 * @license http://hiqdev.com/yii2-hiar/license
 */

namespace hiqdev\hiar;

use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use yii\helpers\Json;
/**
 * QueryBuilder builds an HiActiveResource query based on the specification given as a [[Query]] object.
 */
class QueryBuilder extends \yii\base\Object
{
    private $_sort = [
        SORT_ASC =>'_asc',
        SORT_DESC=>'_desc',
    ];

    public $db;

    public function __construct($connection, $config = [])
    {
        $this->db = $connection;
        parent::__construct($config);
    }

    public function build($query)
    {
        $options = $parts = [];
        if ($query->limit !== null && $query->limit >= 0) {
            $parts['limit'] = $query->limit;
        }
        if ($query->offset > 0) {
            $parts['page'] = ceil($query->offset/$query->limit)+1;
        }
        if (!empty($query->query)) {
            $parts['query'] = $query->limit;
        }

        if (!empty($query->where)) {
            $whereFilter = $this->buildCondition($query->where);
//            \yii\helpers\VarDumper::dump($whereFilter, 10, true);
            $parts = array_merge($parts, $whereFilter);
        }

        if (!empty($query->orderBy)) {
            $parts['orderby'] = key($query->orderBy).$this->_sort[reset($query->orderBy)];
        }

        return [
            'queryParts' => $parts,
            'index' => $query->index,
            'type' => $query->type,
            'options' => $options,
        ];
    }

    public function buildCondition($condition) {

        static $builders = [
            'and'     => 'buildAndCondition',
            'between' => 'buildBetweenCondition',
            'eq'      => 'buildEqCondition',
            'in'      => 'buildInCondition',
            'like'    => 'buildLikeCondition',
        ];
        if (empty($condition)) {
            return [];
        }
        if (!is_array($condition)) {
            throw new NotSupportedException('String conditions in where() are not supported by HiActiveResource.');
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
                $parts[$attribute.'s'] = join(',',$value);
            } else {
                $parts[$attribute] = $value;
            }
        }

        return $parts;
    }

    private function buildLikeCondition ($operator, $operands) {
        if (!isset($operands[0], $operands[1])) {
            throw new InvalidParamException("Operator '$operator' requires three operands.");
        }
        return [$operands[0].'_like' => $operands[1]];
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
        throw new NotSupportedException('Between condition is not supported by HiActiveResource.');
    }

    private function buildInCondition($operator, $operands)
    {
        $key = array_shift($operands);

        return [$key.'_in' => implode(',', array_shift($operands))];
    }

    private function buildEqCondition ($operator, $operands) {
        $key = array_shift($operands);

        return [$key => reset($operands)];
    }

    protected function buildCompositeInCondition($operator, $columns, $values)
    {
        throw new NotSupportedException('composite in is not supported by HiActiveResource.');
    }

}
