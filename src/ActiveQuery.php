<?php

/*
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use yii\base\NotSupportedException;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveQueryTrait;
use yii\db\ActiveRecordInterface;
use yii\db\ActiveRelationTrait;
use yii\helpers\ArrayHelper;

class ActiveQuery extends Query implements ActiveQueryInterface
{
    use ActiveQueryTrait {
        createModels as defaultCreateModels;
    }

    use ActiveRelationTrait;

    /**
     * @event Event an event that is triggered when the query is initialized via [[init()]].
     */
    const EVENT_INIT = 'init';

    /**
     * @var array|null a list of relations that this query should be joined with
     */
    public $joinWith = [];

    /**
     * @var array options for search
     */
    public $options = [];

    /**
     * Constructor.
     *
     * @param string $modelClass the model class associated with this query
     * @param array $config configurations to be applied to the newly created query object
     */
    public function __construct($modelClass, $config = [])
    {
        $this->modelClass = $modelClass;

        parent::__construct($config);
    }

    /**
     * Initializes the object.
     * This method is called at the end of the constructor. The default implementation will trigger
     * an [[EVENT_INIT]] event. If you override this method, make sure you call the parent implementation at the end
     * to ensure triggering of the event.
     */
    public function init()
    {
        parent::init();
        $this->trigger(self::EVENT_INIT);
    }

    /**
     * Creates a DB command that can be used to execute this query.
     *
     * @param Connection $db the DB connection used to create the DB command.
     *                       If null, the DB connection returned by [[modelClass]] will be used.
     *
     * @return Command the created DB command instance.
     */
    public function createCommand($db = null)
    {
        if ($this->primaryModel !== null) {
            // lazy loading
            if (is_array($this->via)) {
                // via relation
                /* @var $viaQuery ActiveQuery */
                list($viaName, $viaQuery) = $this->via;
                if ($viaQuery->multiple) {
                    $viaModels = $viaQuery->all();
                    $this->primaryModel->populateRelation($viaName, $viaModels);
                } else {
                    $model = $viaQuery->one();
                    $this->primaryModel->populateRelation($viaName, $model);
                    $viaModels = $model === null ? [] : [$model];
                }
                $this->filterByModels($viaModels);
            } else {
                $this->filterByModels([$this->primaryModel]);
            }
        }

        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        if ($db === null) {
            $db = $modelClass::getDb();
        }

        if ($this->type === null) {
            $this->type = $modelClass::type();
        }
        if ($this->index === null) {
            $this->index = $modelClass::index();
            $this->type = $modelClass::type();
        }

        $commandConfig = $db->getQueryBuilder()->build($this);

        return $db->createCommand($commandConfig);
    }

    /**
     * @return static
     */
    public function prepare()
    {
        // NOTE: because the same ActiveQuery may be used to build different SQL statements
        // (e.g. by ActiveDataProvider, one for count query, the other for row data query,
        // it is important to make sure the same ActiveQuery can be used to build SQL statements
        // multiple times.
        if (!empty($this->joinWith)) {
            $this->buildJoinWith();
            $this->joinWith = null;
        }

        return $this;
    }

    /**
     * @param $with
     * @return static
     */
    public function joinWith($with)
    {
        $this->joinWith[] = (array) $with;

        return $this;
    }

    private function buildJoinWith()
    {
        $join = $this->join;
        $this->join = [];

        $model = new $this->modelClass;

        foreach ($this->joinWith as $with) {
            $this->joinWithRelations($model, $with);

            foreach ($with as $name => $callback) {
                if (is_int($name)) {
                    $this->join([$callback]);
                } else {
                    $this->join([$name => $callback]);
                }

                unset($with[$name]);
            }
        }

        if (!empty($join)) {
            // append explicit join to joinWith()
            // https://github.com/yiisoft/yii2/issues/2880
            $this->join = empty($this->join) ? $join : array_merge($this->join, $join);
        }

        if (empty($this->select) || true) {
            $this->addSelect(['*' => '*']);
            foreach ($this->joinWith as $join) {
                $key = array_shift(array_keys($join));
                $closure = array_shift($join);

                $this->addSelect(is_int($key) ? $closure : $key);
            }
        }
    }

    /**
     * @param ActiveRecord $model
     * @param $with
     */
    protected function joinWithRelations($model, $with)
    {
        foreach ($with as $name => $callback) {
            if (is_int($name)) {
                $name = $callback;
                $callback = null;
            }

            $primaryModel = $model;
            $parent = $this;

            if (!isset($relations[$name])) {
                $relations[$name] = $relation = $primaryModel->getRelation($name);
                if ($callback !== null) {
                    call_user_func($callback, $relation);
                }
                if (!empty($relation->joinWith)) {
                    $relation->buildJoinWith();
                }
                $this->joinWithRelation($parent, $relation);
            }
        }
    }

    /**
     * Joins a parent query with a child query.
     * The current query object will be modified accordingly.
     * @param ActiveQuery $parent
     * @param ActiveQuery $child
     */
    private function joinWithRelation($parent, $child)
    {
        if (!empty($child->join)) {
            foreach ($child->join as $join) {
                $this->join[] = $join;
            }
        }
    }

    public function select($columns)
    {
        $this->select = $columns;

        return $this;
    }

    /**
     * @param array|string $columns
     * @return $this
     */
    public function addSelect($columns)
    {
        if (!is_array($columns)) {
            $columns = (array) $columns;
        }

        if ($this->select === null) {
            $this->select = $columns;
        } else {
            $this->select = array_merge($this->select, $columns);
        }

        return $this;
    }

    /**
     * Executes query and returns all results as an array.
     *
     * @param Connection $db the DB connection used to create the DB command.
     *                            If null, the DB connection returned by [[modelClass]] will be used.
     *
     * @return array the query results. If the query results in nothing, an empty array will be returned.
     */
    public function all($db = null)
    {
        if ($this->asArray) {
            // TODO implement with
            return parent::all($db);
        }

        $rows = $this->createCommand($db)->search($this->options);

        return $this->populate($rows);
    }

    public function populate($rows)
    {
        if (empty($rows)) {
            return [];
        }

        $models = $this->createModels($rows);
        if (!empty($this->with)) {
            $this->findWith($this->with, $models);
        }

        foreach ($models as $model) {
            $model->afterFind();
        }

        return $models;
    }

    private function createModels($rows)
    {
        $models = [];
        if ($this->asArray) {
            if ($this->indexBy === null) {
                return $rows;
            }
            foreach ($rows as $row) {
                if (is_string($this->indexBy)) {
                    $key = $row[$this->indexBy];
                } else {
                    $key = call_user_func($this->indexBy, $row);
                }
                $models[$key] = $row;
            }
        } else {
            /* @var $class ActiveRecord */
            $class = $this->modelClass;
            if ($this->indexBy === null) {
                foreach ($rows as $row) {
                    $model = $class::instantiate($row);
                    $modelClass = get_class($model);
                    $modelClass::populateRecord($model, $row);
                    $this->populateJoinedRelations($model, $row);
                    $models[] = $model;
                }
            } else {
                foreach ($rows as $row) {
                    $model = $class::instantiate($row);
                    $modelClass = get_class($model);
                    $modelClass::populateRecord($model, $row);
                    $this->populateJoinedRelations($model, $row);
                    if (is_string($this->indexBy)) {
                        $key = $model->{$this->indexBy};
                    } else {
                        $key = call_user_func($this->indexBy, $model);
                    }
                    $models[$key] = $model;
                }
            }
        }

        return $models;
    }

    /**
     * Populates joined relations from [[join]] array.
     *
     * @param ActiveRecord $model
     * @param array $row
     */
    public function populateJoinedRelations($model, array $row)
    {
        foreach ($row as $key => $value) {
            if (empty($this->join) || !is_array($value) || $model->hasAttribute($key)) {
                continue;
            }
            foreach ($this->join as $join) {
                $name = array_shift(array_keys($join));
                $closure = array_shift($join);

                if (is_int($name)) {
                    $name = $closure;
                    $closure = null;
                }
                if ($name !== $key) {
                    continue;
                }
                if ($model->isRelationPopulated($name)) {
                    continue 2;
                }
                $records = [];
                $relation = $model->getRelation($name);
                $relationClass = $relation->modelClass;
                if ($closure !== null) {
                    call_user_func($closure, $relation);
                }
                $relation->prepare();

                if ($relation->multiple) {
                    foreach ($value as $item) {
                        $relatedModel = $relationClass::instantiate($item);
                        $relatedModelClass = get_class($relatedModel);
                        $relatedModelClass::populateRecord($relatedModel, $item);
                        $relation->populateJoinedRelations($relatedModel, $item);
                        $relation->addInverseRelation($relatedModel, $model);
                        if ($relation->indexBy !== null) {
                            $index = is_string($relation->indexBy)
                                ? $relatedModel[$relation->indexBy]
                                : call_user_func($relation->indexBy, $relatedModel);
                            $records[$index] = $relatedModel;
                        } else {
                            $records[] = $relatedModel;
                        }
                    }
                } else {
                    $relatedModel = $relationClass::instantiate($value);
                    $relatedModelClass = get_class($relatedModel);
                    $relatedModelClass::populateRecord($relatedModel, $value);
                    $relation->populateJoinedRelations($relatedModel, $value);
                    $relation->addInverseRelation($relatedModel, $model);
                    $records = $relatedModel;
                }

                $model->populateRelation($name, $records);
            }
        }
    }

    /**
     * @param $relatedModel
     */
    private function addInverseRelation($relatedModel)
    {
        if ($this->inverseOf === null) {
            return;
        }

        $inverseRelation = $relatedModel->getRelation($this->inverseOf);
        $relatedModel->populateRelation($this->inverseOf, $inverseRelation->multiple ? [$this->primaryModel] : $this->primaryModel);
    }

    /**
     * Executes query and returns a single row of result.
     *
     * @param Connection $db the DB connection used to create the DB command.
     *                       If null, the DB connection returned by [[modelClass]] will be used.
     *
     * @return ActiveRecord|array|null a single row of query result. Depending on the setting of [[asArray]],
     *                                 the query result may be either an array or an ActiveRecord object. Null will be returned
     *                                 if the query results in nothing.
     */
    public function one($db = null)
    {
        //        $result = $this->createCommand($db)->get();

        $result = $this->createCommand($db)->search(ArrayHelper::merge(['limit' => 1], $this->options));
        if (empty($result)) {
            return null;
        }
        $result = reset($result);

        if ($this->asArray) {
            // TODO implement with()
//            /* @var $modelClass ActiveRecord */
//            $modelClass = $this->modelClass;
//            $model = $result['_source'];
//            $pk = $modelClass::primaryKey()[0];
//            if ($pk === '_id') {
//                $model['_id'] = $result['_id'];
//            }
//            $model['_score'] = $result['_score'];
//            if (!empty($this->with)) {
//                $models = [$model];
//                $this->findWith($this->with, $models);
//                $model = $models[0];
//            }
            return $result;
        }

        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $model = $class::instantiate($result);
        $class::populateRecord($model, $result);
        $this->populateJoinedRelations($model, $result);
        if (!empty($this->with)) {
            $models = [$model];
            $this->findWith($this->with, $models);
            $model = $models[0];
        }
        $model->afterFind();

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function search($db = null, $options = [])
    {
        $result = $this->createCommand($db)->search($options);
        // TODO implement with() for asArray
        if (!empty($result) && !$this->asArray) {
            $models = $this->createModels($result);
            if (!empty($this->with)) {
                $this->findWith($this->with, $models);
            }
            foreach ($models as $model) {
                $model->afterFind();
            }
            $result = $models;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function column($field, $db = null)
    {
        if ($field === '_id') {
            $command = $this->createCommand($db);
            $command->queryParts['fields'] = [];
            $command->queryParts['_source'] = false;
            $result = $command->search();
            if (empty($result['hits']['hits'])) {
                return [];
            }
            $column = [];
            foreach ($result['hits']['hits'] as $row) {
                $column[] = $row['_id'];
            }

            return $column;
        }

        return parent::column($field, $db);
    }
}
