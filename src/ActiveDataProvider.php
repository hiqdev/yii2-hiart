<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use yii\base\InvalidConfigException;
use yii\db\QueryInterface;

class ActiveDataProvider extends \yii\data\ActiveDataProvider
{
    /**
     * @var ActiveQuery the query that is used to fetch data models and [[totalCount]]
     * if it is not explicitly set
     */
    public $query;

    /**
     * To improve performance, implemented grid summary and pager loading via AJAX when this attribute is `false`
     * There is a possibility set this attribute via DI
     * @see \hipanel\base\SearchModelTrait::search()
     *
     * @var bool
     */
    public bool $countSynchronously = false;

    public function enableSynchronousCount(): void
    {
        $this->countSynchronously = true;
    }

    /**
     * When receiving the pager and summary through AJAX, to calculate the limit and offset of Grid,
     * you need to get the maximum possible total count, otherwise the Grid pages will not switch
     *
     * @return int
     * @throws \yii\base\InvalidConfigException
     */
    protected function prepareTotalCount(): int
    {
        return $this->countSynchronously ? parent::prepareTotalCount() : PHP_INT_MAX;
    }

    public function refresh(bool $keepTotalCount = false): void
    {
        $tc = $this->getTotalCount();
        parent::refresh();
        if ($keepTotalCount) {
            $this->setTotalCount($tc);
        }
    }

    public function prepareQuery(): QueryInterface
    {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }
        $query = clone $this->query;
        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
            $query->limit($pagination->getLimit())->offset($pagination->getOffset());
        }
        if (($sort = $this->getSort()) !== false) {
            $query->addOrderBy($sort->getOrders());
        }

        return $query;
    }
}
