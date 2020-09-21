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
    public function insert($runValidation = true, $attributes = null, array $queryOptions = [])
    {
        if (!$attributes) {
            $attributes = $this->attributes ?: $this->first->activeAttributes();
        }
        if ($runValidation && !$this->validate($attributes)) {
            return false;
        }
        if (!$this->beforeSave(true)) {
            return false;
        }

        $data    = $this->collectData($attributes);
        $results = $this->performOperation('create', $data, $queryOptions);
        $pk      = $this->first->primaryKey()[0];
        foreach ($this->models as $key => $model) {
            $values = &$data[$key];
            $result = &$results[$key];
            if (!$result) {
                $result = $this->findAssociatedModelData($results, $model, $pk);
            }

            $model->{$pk} = $result['id'];
            if ($pk !== 'id') {
                $values[$pk] = $result['id'];
            }
            $changedAttributes = array_fill_keys(array_keys($values), null);
            $model->setOldAttributes($values);
            $model->afterSave(true, $changedAttributes);
        }

        $this->afterSave();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function update($runValidation = true, $attributes = null, array $queryOptions = [])
    {
        if (!$attributes) {
            $attributes = $this->attributes ?: $this->first->activeAttributes();
        }
        if ($runValidation && !$this->validate($attributes)) {
            return false;
        }
        if (!$this->beforeSave()) {
            return false;
        }

        $data    = $this->collectData($attributes);
        $results = $this->performOperation('update', $data, $queryOptions);

        foreach ($this->models as $key => $model) {
            $changedAttributes = [];
            $values            = array_key_exists($key, $data) ? $data[$key] : $data[$model->id]; /// XXX not good
            foreach ($values as $name => $value) {
                $changedAttributes[$name] = $model->getOldAttribute($name);
                $model->setOldAttribute($name, $value);
            }
            $model->afterSave(false, $changedAttributes);
        }

        $this->afterSave();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        if (!$this->beforeDelete()) {
            return false;
        }

        $data    = $this->collectData();
        $results = $this->performOperation('delete', $data);

        $this->afterDelete();

        return $results;
    }

    /**
     * Collects data from the stored models.
     * @param string|array $attributes list of attributes names
     * @return array
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
     * Perform operation with collection models
     * @param string $command (create||update||delete)
     * @param array $data
     * @param array $queryOptions
     * @return array
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
