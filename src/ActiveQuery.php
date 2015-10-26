<?php

/*
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveQueryTrait;
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
     * @var array a list of relations that this query should be joined with
     */
    public $joinWith = [];

    /**
     * Constructor.
     *
     * @param array $modelClass the model class associated with this query
     * @param array $config     configurations to be applied to the newly created query object
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
            $this->type  = $modelClass::type();
        }

        $commandConfig = $db->getQueryBuilder()->build($this);

        return $db->createCommand($commandConfig);
    }

    /**
     * {@inheritdoc}
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
    }

    public function joinWith($with)
    {
        $this->joinWith[] = [(array) $with, true];

        return $this;
    }

    private function buildJoinWith()
    {
        $join = $this->join;

        $this->join = [];

        foreach ($this->joinWith as $config) {
            list($with, $eagerLoading) = $config;

            foreach ($with as $name => $callback) {
                if (is_int($name)) {
                    $this->join($callback);
                    unset($with[$name]);
                } else {
                    throw new NotSupportedException('joinWith() using query modification is not supported, use with() instead.');
                }
            }
        }

        // remove duplicated joins added by joinWithRelations that may be added
        // e.g. when joining a relation and a via relation at the same time
        $uniqueJoins = [];
        foreach ($this->join as $j) {
            $uniqueJoins[serialize($j)] = $j;
        }
        $this->join = array_values($uniqueJoins);

        if (!empty($join)) {
            // append explicit join to joinWith()
            // https://github.com/yiisoft/yii2/issues/2880
            $this->join = empty($this->join) ? $join : array_merge($this->join, $join);
        }

        if (empty($this->select)) {
            $this->addSelect(['*' => '*']);
            foreach ($this->joinWith as $join) {
                $this->addSelect(reset($join));
            }
        }
    }

    public function select($columns)
    {
        $this->select = $columns;

        return $this;
    }

    public function addSelect($columns)
    {
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
     * @param Connection $db      the DB connection used to create the DB command.
     *                            If null, the DB connection returned by [[modelClass]] will be used.
     * @param array      $options Options that will be passed to search command
     *
     * @return array the query results. If the query results in nothing, an empty array will be returned.
     */
    public function all($db = null, $options = [])
    {
        if ($this->asArray) {
            // TODO implement with
            return parent::all($db);
        }

        $result = $this->createCommand($db)->search($options);

        if (empty($result)) {
            return [];
        }

        $models = $this->createModels($result);
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
                    $model      = $class::instantiate($row);
                    $modelClass = get_class($model);
                    $modelClass::populateRecord($model, $row);
                    $this->populateJoinedRelations($model, $row);
                    $models[] = $model;
                }
            } else {
                foreach ($rows as $row) {
                    $model      = $class::instantiate($row);
                    $modelClass = get_class($model);
                    $modelClass::populateRecord($model, $row);
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
     * @param array        $row
     */
    public function populateJoinedRelations($model, array $row)
    {
        foreach ($row as $key => $value) {
            if (empty($this->join) || !is_array($value) || $model->hasAttribute($key)) {
                continue;
            }
            foreach ($this->join as $name) {
                if ($model->isRelationPopulated($name)) {
                    continue 2;
                }
                $records       = [];
                $relation      = $model->getRelation($name);
                $relationClass = $relation->modelClass;

                if ($relation->multiple) {
                    foreach ($value as $item) {
                        $relationModel      = $relationClass::instantiate($item);
                        $relationModelClass = get_class($relationModel);
                        $relationModelClass::populateRecord($relationModel, $item);
                        $relation->populateJoinedRelations($relationModel, $item);
                        $records[] = $relationModel;
                    }
                } else {
                    $relationModel      = $relationClass::instantiate($value);
                    $relationModelClass = get_class($relationModel);
                    $relationModelClass::populateRecord($relationModel, $value);
                    $relation->populateJoinedRelations($relationModel, $value);
                    $records = $relationModel;
                }

                $model->populateRelation($name, $records);
            }
        }
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

        if (($result = parent::one($db)) === false) {
            return;
        }
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
        } else {
            /* @var $class ActiveRecord */
            $class = $this->modelClass;
            $model = $class::instantiate($result);
            $class::populateRecord($model, $result);
            if (!empty($this->with)) {
                $models = [$model];
                $this->findWith($this->with, $models);
                $model = $models[0];
            }
            $model->afterFind();

            return $model;
        }
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
            $command                        = $this->createCommand($db);
            $command->queryParts['fields']  = [];
            $command->queryParts['_source'] = false;
            $result                         = $command->search();
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

    public function getList($as_array = true, $db = null, $options = [])
    {
        $rawResult = $this->createCommand($db)->getList($options);
        foreach ($rawResult as $k => $v) {
            $result[] = ['gl_key' => $k, 'gl_value' => $v];
        }
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
        $result = $result ?: [];

//        return $this->createCommand($db)->getList($options);
        return $as_array ? ArrayHelper::map($result, 'gl_key', function ($o) {
            return Yii::t('app', $o->gl_value);
        }) : $result;
    }
}
