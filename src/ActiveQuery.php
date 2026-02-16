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
use hiqdev\hiart\rest\QueryBuilder;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveQueryTrait;
use yii\db\ActiveRelationTrait;
use yii\db\BaseActiveRecord;

class ActiveQuery extends Query implements ActiveQueryInterface
{
    use ActiveQueryTrait;
    use ActiveRelationTrait;

    /**
     * @event Event an event that is triggered when the query is initialized via [[init()]].
     */
    const string EVENT_INIT = 'init';

    /**
     * @var array|null a list of relations that this query should be joined with
     */
    public array|null $joinWith = [];

    /**
     * @var bool flag to track if joinWith has been processed
     */
    private bool $joinWithProcessed = false;

    /**
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
     * @param null $db the DB connection used to create the DB command.
     * If null, the DB connection returned by [[modelClass]] will be used.
     * @return Command the created DB command instance
     * @throws \Exception
     */
    public function createCommand($db = null)
    {
        if ($this->primaryModel !== null) {
            // lazy loading
            if (is_array($this->via)) {
                // via relation
                /** @var ActiveQuery $viaQuery */
                [$viaName, $viaQuery] = $this->via;
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
        if ($this->from === null) {
            $this->from = $modelClass::tableName();
        }

        return parent::createCommand($db);
    }

    /**
     * Prepares a query for use. See NOTE.
     * @param QueryBuilder $builder
     * @return static
     */
    public function prepare($builder = null)
    {
        // NOTE: because the same ActiveQuery may be used to build different SQL statements
        // (e.g. by ActiveDataProvider, one for the count query, the other for row data query,
        // it is important to make sure the same ActiveQuery can be used to build SQL statements
        // multiple times.
        if (!empty($this->joinWith) && !$this->joinWithProcessed) {
            $this->buildJoinWith();
            $this->joinWithProcessed = true;
        }

        return $this;
    }

    /**
     * @param array|string $with
     * @return static
     */
    public function joinWith(array|string $with): self
    {
        $this->joinWith[] = (array)$with;

        return $this;
    }

    protected function buildJoinWith(): void
    {
        $join = $this->join;
        $this->join = [];

        $modelClass = $this->modelClass;
        $model = new $modelClass();

        foreach ($this->joinWith as $with) {
            $this->joinWithRelations($model, $with);

            foreach ($with as $name => $callback) {
                if (is_int($name)) {
                    $this->innerJoin([$callback]);
                } else {
                    $this->innerJoin([$name => $callback]);
                }
            }
        }

        if (!empty($join)) {
            // append explicit join to joinWith()
            // https://github.com/yiisoft/yii2/issues/2880
            $this->join = empty($this->join) ? $join : array_merge($this->join, $join);
        }

        if (empty($this->select)) {
            $this->addSelect(['*' => '*']);
        }

        if (empty($this->joinWith)) {
            return;
        }

        foreach ($this->joinWith as $join) {
            $keys = array_keys($join);
            $key = array_shift($keys);
            $closure = array_shift($join);

            $this->addSelect(\is_int($key) ? $closure : $key);
        }
    }

    /**
     * @param ActiveRecord $model
     * @param array $with
     * @return void
     */
    private function joinWithRelations(ActiveRecord $model, array $with): void
    {
        $relations = [];
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
                    $callback($relation);
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
     * @return void
     */
    private function joinWithRelation($parent, $child): void
    {
        if (!empty($child->join)) {
            foreach ($child->join as $join) {
                $this->join[] = $join;
            }
        }
    }

    public function select($columns, $option = null)
    {
        $this->select = $columns;

        return $this;
    }

    /**
     * @param array|string $columns
     * @return $this
     */
    public function addSelect($columns): self
    {
        if (!is_array($columns)) {
            $columns = (array)$columns;
        }

        if ($this->select === null) {
            $this->select = $columns;
        } else {
            $this->select = array_merge($this->select, $columns);
        }

        return $this;
    }

    /**
     * Executes query and returns a single row of result.
     *
     * @param AbstractConnection $db the DB connection used to create the DB command.
     * If null, the DB connection returned by [[modelClass]] will be used.
     * @return ActiveRecord|array|null a single row of query result. Depending on the setting of [[asArray]],
     * the query result may be either an array or an ActiveRecord object. Null will be returned
     * if the query results in nothing.
     */
    public function one($db = null)
    {
        if ($this->asArray) {
            return parent::one($db);
        }

        $row = $this->searchOne($db);
        if ($row === null) {
            return null;
        }
        $models = $this->populate([$row]);

        return reset($models);
    }

    /**
     * Executes query and returns all results as an array.
     * @param AbstractConnection $db the DB connection used to create the DB command.
     * If null, the DB connection returned by [[modelClass]] will be used.
     * @return array|ActiveRecord[] the query results. If the query results in nothing, an empty array will be returned.
     */
    public function all($db = null)
    {
        if ($this->asArray) {
            return parent::all($db);
        }

        $rows = $this->searchAll($db);

        return $this->populate($rows);
    }

    /**
     * Converts raw query results into model instances.
     * @param array $rows
     * @return array
     */
    public function populate($rows): array
    {
        if (empty($rows)) {
            return [];
        }

        $models = $this->createModels($rows);

        // Force garbage collection after freeing row data
        // This ensures freed memory is actually released before loading relations
        // This frees approximately ~2MB
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        if (!empty($this->with)) {
            $this->findWith($this->with, $models);
        }

        foreach ($models as $model) {
            $model->afterFind();
        }

        return $models;
    }

    /**
     * Creates model instances from raw data rows.
     * Memory optimized: unsets processed rows to free memory during iteration
     * @param array $rows
     * @return array
     */
    private function createModels(array $rows): array
    {
        $models = [];
        $class = $this->modelClass;
        $indexBy = $this->indexBy;
        $isIndexByClosure = $indexBy instanceof Closure;

        foreach ($rows as $row) {
            $model = $this->instantiateAndPopulateModel($class, $row);
            $this->populateJoinedRelations($model, $row);

            $key = $this->resolveModelKey($model, $indexBy, $isIndexByClosure);
            if ($key !== null) {
                $models[$key] = $model;
            } else {
                $models[] = $model;
            }
        }

        return $models;
    }

    private function instantiateAndPopulateModel(string $class, array $row): ActiveRecord
    {
        /** @var ActiveRecord $model */
        $model = $class::instantiate($row);
        $modelClass = get_class($model);
        $modelClass::populateRecord($model, $row);

        return $model;
    }

    private function resolveModelKey(ActiveRecord $model, $indexBy, bool $isIndexByClosure)
    {
        if ($indexBy === null) {
            return null;
        }

        return $isIndexByClosure ? $indexBy($model) : $model->{$indexBy};
    }

    private function buildJoinsMap(): array
    {
        $joinsMap = [];
        foreach ($this->join as $join) {
            $keys = array_keys($join);
            $name = array_shift($keys);
            $closure = array_shift($join);
            if (is_int($name)) {
                $name = $closure;
                $closure = null;
            }
            $joinsMap[$name] = $closure;
        }

        return $joinsMap;
    }

    private function populateJoinedRelations(ActiveRecord $model, array $row): void
    {
        if (empty($this->join)) {
            return;
        }

        $joinsMap = $this->buildJoinsMap();

        foreach ($row as $key => $value) {
            if (!is_array($value) || !array_key_exists($key, $joinsMap) || $model->hasAttribute($key)) {
                continue;
            }

            if ($model->isRelationPopulated($key)) {
                continue;
            }

            $closure = $joinsMap[$key];
            $relation = $model->getRelation($key);
            $relationClass = $relation->modelClass;

            if ($closure !== null) {
                $closure($relation);
            }
            $relation->prepare();

            $records = $relation->multiple
                ? $this->populateMultipleRelatedModels($relationClass, $value, $relation)
                : $this->populateSingleRelatedModel($relationClass, $value, $relation);

            $model->populateRelation($key, $records);
        }
    }

    private function populateMultipleRelatedModels(string $relationClass, array &$value, self $relation): array
    {
        $records = [];
        $indexBy = $relation->indexBy;
        $isIndexByString = is_string($indexBy);

        foreach ($value as $itemKey => $item) {
            $relatedModel = $this->instantiateAndPopulateModel($relationClass, $item);
            $relation->populateJoinedRelations($relatedModel, $item);
            $relation->addInverseRelation($relatedModel);
            $relatedModel->trigger(BaseActiveRecord::EVENT_AFTER_FIND);

            $index = $this->resolveRelatedModelKey($relatedModel, $indexBy, $isIndexByString);
            if ($index !== null) {
                $records[$index] = $relatedModel;
            } else {
                $records[] = $relatedModel;
            }
            unset($value[$itemKey]);
        }

        return $records;
    }

    private function populateSingleRelatedModel(string $relationClass, array $value, self $relation): ActiveRecord
    {
        $relatedModel = $this->instantiateAndPopulateModel($relationClass, $value);
        $relation->populateJoinedRelations($relatedModel, $value);
        $relation->addInverseRelation($relatedModel);
        $relatedModel->trigger(BaseActiveRecord::EVENT_AFTER_FIND);

        return $relatedModel;
    }

    private function resolveRelatedModelKey(ActiveRecord $model, $indexBy, bool $isIndexByString)
    {
        if ($indexBy === null) {
            return null;
        }

        return $isIndexByString ? $model->{$indexBy} : $indexBy($model);
    }

    /**
     * Adds inverse relation to the related model.
     * @param ActiveRecord $relatedModel
     * @return void
     */
    private function addInverseRelation(ActiveRecord $relatedModel): void
    {
        if ($this->inverseOf === null) {
            return;
        }

        $inverseRelation = $relatedModel->getRelation($this->inverseOf);
        $relatedModel->populateRelation($this->inverseOf, $inverseRelation->multiple ? [$this->primaryModel] : $this->primaryModel);
    }
}
