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

use Yii;
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

    /**
     * @var bool|mixed
     */
    private bool $loadModels = true;

    public function enableSynchronousCount(): void
    {
        $this->countSynchronously = true;
    }

    public function preventLoadModels(): void
    {
        $this->loadModels = false;
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
        if ($this->countSynchronously) {
            $app = Yii::$app;

            return $app->getCache()->getOrSet([$app->user->getId(), $this->query->modelClass, __METHOD__], fn(): int => parent::prepareTotalCount(), 5);
        }

        return PHP_INT_MAX;
    }

    /** {@inheritdoc} */
    protected function prepareModels(): array
    {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }
        $query = clone $this->query;
        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
            if ($pagination->totalCount === 0) {
                return [];
            }
            $query->limit($pagination->getLimit())->offset($pagination->getOffset());
        }
        if (($sort = $this->getSort()) !== false) {
            $query->addOrderBy($sort->getOrders());
        }

        return $this->loadModels ? $query->all($this->db) : array_pad([], $pagination->pageSize, 0);
    }
}
