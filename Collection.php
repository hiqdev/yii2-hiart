<?php
/**
 * @link http://hiqdev.com/yii2-hiar
 * @copyright Copyright (c) 2015 HiQDev
 * @license http://hiqdev.com/yii2-hiar/license
 */

namespace hiqdev\hiar;

use common\components\Err;
use yii\helpers\ArrayHelper;
use yii\base\Component;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;
use yii\base\Model;
use yii\base\ModelEvent;
use yii\helpers\Json;

/**
 * Class Collection manages the collection of the models
 */
class Collection extends Component
{
    const EVENT_BEFORE_INSERT   = 'beforeInsert';
    const EVENT_BEFORE_UPDATE   = 'beforeUpdate';
    const EVENT_BEFORE_VALIDATE = 'beforeValidate';
    const EVENT_AFTER_VALIDATE  = 'afterValidate';
    const EVENT_AFTER_SAVE      = 'afterSave';
    const EVENT_BEFORE_LOAD     = 'beforeLoad';
    const EVENT_AFTER_LOAD      = 'afterLoad';

    /**
     * @var array of models
     */
    public $models;

    /**
     * @var string the name of the form. Sets automatically on [[set()]]
     * @see set()
     */
    public $formName;

    /**
     * @var callable the function to format loaded data. Gets three attributes:
     *  - model (instance of operating model)
     *  - key   - the key of the loaded item
     *  - value - the value of the loaded item
     *
     * Should return array, where the first item is the new key, and the second - a new value. Example:
     *
     * ```
     * return [$key, $value];
     * ```
     */
    public $loadFormatter;

    /**
     * @var \yii\base\Model the template model instance. May be set manually by [[setModel()]] or
     * automatically on [[set()]] call
     * @see setModel()
     * @see set()
     */
    protected $model;

    /**
     * @var array options that will be passed to the new model when loading data in [[load]]
     * @see load()
     */
    public $modelOptions = [];

    /**
     * @var ActiveRecord the first model of the set. Fills automatically by [[set()]]
     * @see set()
     */
    public $first;

    /**
     * @var array the model's attributes that will be saved
     */
    public $attributes;

    /**
     * Sets the model of the collection
     *
     * @param Model|array $model if the model is an instance of [[Model]] - sets it, otherwise - creates the model
     * using given options array
     * @return object|Model
     * @throws InvalidConfigException
     */
    public function setModel ($model) {
        if ($model instanceof Model) {
            $this->model = $model;
        } else {
            $this->model = \Yii::createObject($model);
        }
        $this->updateFormName();

        return $this->model;
    }

    /**
     * Sets the scenario of the default model
     *
     * @param $value string scenario
     */
    public function setScenario ($value) {
        $this->modelOptions['scenario'] = $value;
    }

    /**
     * Gets the scenario the default model
     *
     * @return string the scenario
     */
    public function getScenario () {
        return $this->modelOptions['scenario'];
    }

    /**
     * Updates [[formName]] from the current [[model]]
     *
     * @return string the form name
     */
    public function updateFormName () {
        if (!($this->model instanceof Model)) {
            throw new InvalidCallException('The model should be set first');
        }

        return $this->formName = $this->model->formName();
    }

    /**
     * We can load data from 3 different structures:
     *
     * 1) POST: [
     *     'ModelName' => [
     *         'attribute1' => 'value1',
     *         'attribute2' => 'value2'
     *     ]
     * ]
     *
     * 2) POST: [
     *      'ModelName' => [
     *          1   => [
     *              'attribute1' => 'value1',
     *              'attribute2' => 'value2'
     *          ],
     *          2   => [
     *              ...
     *          ]
     *      ]
     * }
     *
     * @param array|callable $data - the data to be proceeded.
     * If is callable - gets arguments:
     *   - model
     *   - fromName
     * @return Collection
     * @throws InvalidConfigException
     */
    public function load ($data = null) {
        $models    = [];
        $finalData = [];

        if ($data === null) {
            $data = \Yii::$app->request->post();
            if (isset($data[$this->formName])) {
                $data = [$data[$this->formName]];
            }
        } elseif ($data instanceof \Closure) {
            $data = call_user_func($data, $this->model, $this->formName);
        }

        foreach ($data as $key => $value) {
            if ($this->loadFormatter instanceof \Closure) {
                $item = call_user_func($this->loadFormatter, $this->model, $key, $value);
                $key  = $item[0];
            } else {
                $item = [$key, $value];
            }
            $options      = ArrayHelper::merge(['class' => $this->model->className()], $this->modelOptions);
            $models[$key] = \Yii::createObject($options);

            $finalData[$this->formName][$key] = $item[1];
        }
        $this->model->loadMultiple($models, $finalData);

        return $this->set($models);
    }

    /**
     * Sets the array of AR models to the collection
     *
     * @param array $models - array of AR Models
     * @return $this
     */
    public function set (array $models) {
        /* @var $first ActiveRecord */
        $first = reset($models);
        if ($first === false) {
            return $this;
        }
        $this->first = $first;

        /// redo
        $this->formName = $first->formName();
        $this->model    = $first->className();

        $this->models = $models;
        if (!$this->isConsistent()) {
            throw new InvalidValueException('Models are not objects of same class or not follow same operation');
        }

        return $this;
    }

    /**
     * Saves the current collection
     *
     * This method will call [[insert()]] or [[update()]].
     *
     * @param boolean $runValidation whether to perform validation before saving the collection.
     * @param array $attributes list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded will be saved. If the scenario is specified, will use only
     * fields from the scenario
     * @param array $options the array of options that will be passed to [[insert]] or [[update]] methods to override
     * model parameters.
     * @return boolean whether the saving succeeds
     * @throws HiResException
     */
    public function save ($runValidation = true, $attributes = null, $options = []) {
        if ($this->isEmpty()) {
            throw new InvalidCallException('Collection is empty, nothing to save');
        }

        if ($this->first->getIsNewRecord()) {
            return $this->insert($runValidation, $attributes, $options);
        } else {
            return $this->update($runValidation, $attributes, $options);
        }
    }

    public function insert ($runValidation = true, $attributes = null, $options = []) {
        if (!$attributes) {
            $attributes = $this->attributes ?: $this->first->activeAttributes();
        }
        if ($runValidation && !$this->validate($attributes)) {
            return false;
        }
        if (!$this->beforeSave(true)) {
            return false;
        }

        $data    = $this->collectData($attributes, $options);
        $command = $this->first->getScenarioCommand('create', true);

        $results = $this->first->getDb()->createCommand()->perform($command, $data);

        if (Err::isError($results)) {
            throw new HiResException(Err::getError($results), Json::encode($results));
        }

        $pk = $this->first->primaryKey()[0];
        foreach ($this->models as $key => $model) {
            /* @var $model ActiveRecord */
            $values = &$data[$key];
            $result = &$results[$key];

            $model->{$pk} = $result['id'];
            if ($pk != 'id') {
                $values[$pk] = $result['id'];
            }
            $changedAttributes = array_fill_keys(array_keys($values), null);
            $model->setOldAttributes($values);
            $model->afterSave(true, $changedAttributes);
        }

        $this->afterSave(true);

        return true;
    }

    public function update ($runValidation = true, $attributes = null, $options = []) {
        if (!$attributes) {
            $attributes = $this->attributes ?: $this->first->activeAttributes();
        }
        if ($runValidation && !$this->validate($attributes)) {
            return false;
        }
        if (!$this->beforeSave()) {
            return false;
        };

        $data    = $this->collectData($attributes, $options);
        $command = $this->first->getScenarioCommand('update', true);

        $results = $this->first->getDb()->createCommand()->perform($command, $data);

        if ($results === false || Err::isError($results)) {
            throw new HiResException(Err::getError($results), Json::encode($results));
        }

        foreach ($this->models as $key => $model) {
            $changedAttributes = [];
            $values            = &$data[$key];
            foreach ($values as $name => $value) {
                /* @var $model ActiveRecord */
                $changedAttributes[$name] = $model->getOldAttribute($name);
                $model->setOldAttribute($name, $value);
            }
            $model->afterSave(false, $changedAttributes);
        }

        $this->afterSave();

        return true;
    }

    /**
     * Collects data from the stored models
     *
     * @param string|array $attributes list of attributes names
     * @param callable|array $options overrides the model attributes
     * If is array - merges with the model attributes
     * If is callable - gets two arguments:
     *   1) the array of model attributes
     *   2) the model object
     * @return array
     */
    public function collectData ($attributes = null, $options = []) {
        $data       = [];
        $attributes = (array)$attributes;
        foreach ($this->models as $model) {
            /* @var $model ActiveRecord */
            $key = $model->getPrimaryKey();
            if ($options instanceof \Closure) {
                $row = call_user_func($options, $model->getAttributes($attributes), $model);
            } else {
                $row = array_merge($model->getAttributes($attributes), $options);
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
     * Whether one of models has an error
     *
     * @return bool
     */
    public function hasErrors () {
        foreach ($this->models as $model) {
            /* @var $model ActiveRecord */
            if ($model->hasErrors()) {
                return true;
            }
        }

        return false;
    }

    public function validate ($attributes = null) {
        if (!$this->beforeValidate()) {
            return false;
        }

        $this->first->validateMultiple($this->models, $attributes);

        $this->afterValidate();

        return true;
    }

    public function beforeValidate () {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_VALIDATE, $event);

        return $event->isValid;
    }

    public function afterValidate () {
        $event = new ModelEvent();

        $this->trigger(self::EVENT_AFTER_VALIDATE, $event);

        return $event->isValid;
    }

    public function beforeSave ($insert = false) {
        $event = new ModelEvent();
        if ($this->isEmpty()) {
            $event->isValid = false;
        }
        $this->trigger($insert ? self::EVENT_BEFORE_INSERT : self::EVENT_BEFORE_UPDATE, $event);

        return $event->isValid;
    }

    public function afterSave () {
        $this->trigger(self::EVENT_AFTER_SAVE);
    }


    public function beforeLoad () {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_LOAD, $event);

        return $event->isValid;
    }

    public function afterLoad () {
        $this->trigger(self::EVENT_AFTER_LOAD);
    }

    /**
     * Iterates over all of the models and triggers some event
     *
     * @param string $name the event name
     * @param ModelEvent $event
     * @return bool whether is valid
     */
    public function triggerAll ($name, ModelEvent $event = null) {
        if ($event == null) {
            $event = new ModelEvent();
        }
        foreach ($this->models as $model) {
            /* @var $model ActiveRecord */
            $model->trigger($name, $event);
        }

        return $event->isValid;
    }

    public function isConsistent () {
        $new       = $this->first->getIsNewRecord();
        $className = $this->first->className();
        foreach ($this->models as $model) {
            /* @var $model ActiveRecord */
            if ($new != $model->getIsNewRecord() || $className != $model->className()) {
                return false;
            }
        }

        return true;
    }

    public function isEmpty () {
        return empty($this->models);
    }

}
