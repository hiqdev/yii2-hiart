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

use Closure;

/**
 * {@inheritdoc}
 */
class ExtendedCollection extends Collection
{
    /**
     * {@inheritdoc}
     */
    public $checkConsistency = false;

    /**
     * {@inheritdoc}
     */
    public function collectData($attributes = null)
    {
        $data = [];
        foreach ($this->models as $model) {
            if ($this->dataCollector instanceof Closure) {
                list($key, $row) = call_user_func($this->dataCollector, $model, $this);
            } else {
                $key = $model->getPrimaryKey();
                $row = $model->getAttributes($this->isConsistent() ? $attributes : $model->attributes);
                $row['recordModel'] = $model;
            }

            if ($key) {
                $data[$key] = $row;
            } else {
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function performOperation(string $command, array $data, array $queryOptions = []) : array
    {
        if ($this->isConsistent()) {
            return $this->first->batchQuery($command, $data, $queryOptions);
        }

        foreach ($data as $key => $record) {
            $records[$record['recordModel']->className()][$key] = $record;
        }

        $results = [];
        foreach ($records as $className => $d) {
            $row = reset($d);
            $model = $d['recordModel'];
            $results = array_merge($results, $model->batchQuery($command, $d, $queryOptions));
        }

        return $results;
    }
}
