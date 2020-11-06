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

    public bool $useRealCount = false;

    public function enableCount(): void
    {
        $this->useRealCount = true;
    }

    protected function prepareTotalCount()
    {
        return $this->useRealCount ? parent::prepareTotalCount() : $this->getPagination()->pageSize + 1;
    }
}
