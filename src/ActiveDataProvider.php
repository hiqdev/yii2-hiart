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

    protected function prepareTotalCount()
    {
        return $this->countSynchronously ? parent::prepareTotalCount() : 999999;
    }
}
