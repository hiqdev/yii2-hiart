<?php
/**
 * @link http://hiqdev.com/yii2-hiar
 * @copyright Copyright (c) 2015 HiQDev
 * @license http://hiqdev.com/yii2-hiar/license
 */

namespace hiqdev\hiar;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\db\BaseActiveRecord;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use common\components\Err;

class ActiveRecord extends BaseActiveRecord
{
    public $gl_key;
    public $gl_value;

    /**
     * Returns the database connection used by this AR class.
     * By default, the "hiresoruce" application component is used as the database connection.
     * You may override this method if you want to use a different database connection.
     *
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb () {
        return \Yii::$app->get('hiresource');
    }

    /**
     * @inheritdoc
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find () {
        return \Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public static function findOne ($condition) {
        $query = static::find();
        if (is_array($condition)) {
            return $query->andWhere($condition)->one();
        } else {
            return static::get($condition);
        }

    }

    public function isScenarioDefault () {
        return $this->scenario === static::SCENARIO_DEFAULT;
    }

    /**
     * Gets a record by its primary key.
     *
     * @param mixed $primaryKey the primaryKey value
     * @param array $options options given in this parameter are passed to elasticsearch
     * as request URI parameters.
     * Please refer to the [elasticsearch documentation](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-get.html)
     * for more details on these options.
     * @return null|static The record instance or null if it was not found.
     * @throws HiResException
     */
    public static function get ($primaryKey, $options = []) {
        if ($primaryKey === null) {
            return null;
        }
        $command = static::getDb()->createCommand();
        $result  = $command->get(static::modelName(), $primaryKey, $options);
        if (Err::isError($result)) {
            throw new HiResException('Hiresource method: get', Err::getError($result));
        }
        if ($result) {
            $model = static::instantiate($result);
            static::populateRecord($model, $result);
            $model->afterFind();

            return $model;
        }

        return null;
    }

    /**
     * This method defines the attribute that uniquely identifies a record.
     *
     * The primaryKey for elasticsearch documents is the `_id` field by default. This field is not part of the
     * ActiveRecord attributes so you should never add `_id` to the list of [[attributes()|attributes]].
     *
     * You may override this method to define the primary key name when you have defined
     * [path mapping](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/mapping-id-field.html)
     * for the `_id` field so that it is part of the `_source` and thus part of the [[attributes()|attributes]].
     *
     * Note that elasticsearch only supports _one_ attribute to be the primary key. However to match the signature
     * of the [[\yii\db\ActiveRecordInterface|ActiveRecordInterface]] this methods returns an array instead of a
     * single string.
     *
     * @return string[] array of primary key attributes. Only the first element of the array will be used.
     */
    public static function primaryKey () {
        return ['id'];
    }

    /**
     * +     * The name of the main attribute
     * +     *
     * Examples:
     *
     * This will directly reference to the attribute 'name'
     * ```
     *     return 'name';
     * ```
     *
     * This will concatenate listed attributes, separated with `delimiter` value.
     * If delimiter is not set, space is used by default.
     * ```
     *     return ['seller', 'client', 'delimiter' => '/'];
     * ```
     *
     * The callable method, that will get [[$model]] and should return value of name attribute
     * ```
     *     return function ($model) {
     *        return $model->someField ? $model->name : $model->otherName;
     *     };
     * ```
     *
     * @return string|callable|array
     * @throws InvalidConfigException
     * @author SilverFire
     */
    public function primaryValue () {
        return static::formName();
    }

    /**
     * Returns the value of the primary attribute
     *
     * @return mixed|null
     * @throws InvalidConfigException
     * @see primaryValue()
     */
    public function getPrimaryValue () {
        $primaryValue = $this->primaryValue();

        $result = null;
        if ($primaryValue instanceof \Closure) {
            return call_user_func($primaryValue, [$this]);
        } else if (is_array($primaryValue)) {
            $delimiter = ArrayHelper::remove($primaryValue, 'delimiter', ' ');

            return implode($delimiter, $this->getAttributes($primaryValue));
        } else {
            return $this->getAttribute($primaryValue);
        }
    }


    /**
     * Returns the list of all attribute names of the model.
     *
     * This method must be overridden by child classes to define available attributes.
     *
     * Attributes are names of fields of the corresponding elasticsearch document.
     * The primaryKey for elasticsearch documents is the `_id` field by default which is not part of the attributes.
     * You may define [path mapping](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/mapping-id-field.html)
     * for the `_id` field so that it is part of the `_source` fields and thus becomes part of the attributes.
     *
     * @return string[] list of attribute names.
     * @throws \yii\base\InvalidConfigException if not overridden in a child class.
     */
    public function attributes () {
        throw new InvalidConfigException('The attributes() method of Hiresource ActiveRecord has to be implemented by child classes.');
    }

    /**
     * A list of attributes that should be treated as array valued when retrieved through [[ActiveQuery::fields]].
     *
     * If not listed by this method, attributes retrieved through [[ActiveQuery::fields]] will converted to a scalar value
     * when the result array contains only one value.
     *
     * @return string[] list of attribute names. Must be a subset of [[attributes()]].
     */
    public function arrayAttributes () {
        return [];
    }

    /**
     * @return string the name of the index this record is stored in.
     */
    public static function index () {
//        return Inflector::pluralize(Inflector::camel2id(StringHelper::basename(get_called_class()), '-'));
        return mb_strtolower(StringHelper::basename(get_called_class()) . 's');
    }

    /**
     * @return string the name of the type of this record.
     */
    public static function type () {
        return Inflector::camel2id(StringHelper::basename(get_called_class()), '-');
    }

    /**
     * Declares the name of the model associated with this class.
     * By default this method returns the class name by calling [[Inflector::camel2id()]]
     *
     * @return string the module name
     */
    public static function modelName () {
        return Inflector::camel2id(StringHelper::basename(get_called_class()));
    }

    public function insert ($runValidation = true, $attributes = null, $options = []) {
        if ($runValidation && !$this->validate($attributes)) {
            return false;
        }

        if (!$this->beforeSave(true)) {
            return false;
        }

        $values = $this->getDirtyAttributes($attributes);

        $command = $this->getScenarioCommand('create');
        $data    = array_merge($values, $options, ['id' => $this->getOldPrimaryKey()]);

        $response = static::getDb()->createCommand()->perform($command, $data);

        if (Err::isError($response)) {
            throw new HiResException('Hiresource method: Insert -- ' . Json::encode($response), Err::getError($response));
        }
        $pk        = static::primaryKey()[0];
        $this->$pk = $response['id'];
        if ($pk != 'id') {
            $values[$pk] = $response['id'];
        }
        $changedAttributes = array_fill_keys(array_keys($values), null);
        $this->setOldAttributes($values);
        $this->afterSave(true, $changedAttributes);

        return true;
    }

    public function delete ($options = []) {
        if (!$this->beforeDelete()) {
            return false;
        }

        $command = $this->getScenarioCommand('delete');
        $data    = array_merge($options, ['id' => $this->getOldPrimaryKey()]);

        $result = static::getDb()->createCommand()->perform($command, $data);

        if (Err::isError($result)) {
            throw new HiResException('Hiresource method: Delete -- ' . Json::encode($result), Err::getError($result));
        }

        $this->setOldAttributes(null);
        $this->afterDelete();

        if ($result === false) {
            return 0;
        } else {
            return 1;
        }
    }

    public function update ($runValidation = true, $attributeNames = null, $options = []) {
        if ($runValidation && !$this->validate($attributeNames)) {
            return false;
        }

        return $this->updateInternal($attributeNames, $options);
    }

    protected function updateInternal ($attributes = null, $options = []) {
        if (!$this->beforeSave(false)) {
            return false;
        }

        $values = $this->getAttributes($attributes);
//        $values = $this->attributes;

        if (empty($values)) {
            $this->afterSave(false, $values);

            return 0;
        }

        $command = $this->getScenarioCommand('update');
        $data    = array_merge($values, $options, ['id' => $this->getOldPrimaryKey()]);

        $result = static::getDb()->createCommand()->perform($command, $data);

        $changedAttributes = [];
        foreach ($values as $name => $value) {
            $changedAttributes[$name] = $this->getOldAttribute($name);
            $this->setOldAttribute($name, $value);
        }

        $this->afterSave(false, $changedAttributes);

        if ($result === false || Err::isError($result)) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Custom method for HiResource
     *
     * @param $action
     * @param array $options
     * @param bool $bulk
     * @return array
     * @throws HiResException
     */
    public static function perform ($action, $options = [], $bulk = false) {
        $action = ($bulk == true) ? static::index() . $action : static::modelName() . $action;
        $result = static::getDb()->createCommand()->perform($action, $options);
        if (Err::isError($result)) {
            throw new HiResException('Hiresource method: ' . $action, Err::getError($result));
        }

        return $result;
    }

    /**
     * Creates the command name for the specified scenario name
     *
     * @param string $default
     * @param bool $bulk
     * @return string
     * @throws InvalidConfigException
     * @throws NotSupportedException
     */
    public function getScenarioCommand ($default = '', $bulk = false) {
        if ($this->isScenarioDefault()) {
            if ($default !== '') {
                $result = Inflector::id2camel($default);
            } else {
                throw new InvalidConfigException('Scenario not specified');
            }
        } else {
            $scenarioCommands = static::scenarioCommands();
            if ($command = $scenarioCommands[$this->scenario]) {
                if ($command === false) {
                    throw new NotSupportedException('The scenario can not be saved');
                } elseif (is_array($command) && $command[0] === null) {
                    $command = $command[1];
                }
                $result = ucfirst($command);
            } else {
                $result = Inflector::id2camel($this->scenario);
            }
        }

        if (is_array($result)) {
            return implode('', $result);
        } else {
            return static::modelName() . ($bulk ? 's' : '') . $result;
        }
    }

    /**
     * Define an array of relations between scenario and API call action.
     *
     * Example:
     *
     * ```
     * [
     *      'update-name'                => 'set-name',
     *      'update-related-name'        => [Action::formName(), 'SetName'],
     *      'update-self-case-sensetive' => [null, 'SomeSENSETIVE']
     * ]
     * ~~
     *
     *  key string name of scenario
     *  value string|array
     *              string will be passed to [[Inflector::id2camel|id2camel]] inflator
     *              array - first attribute a module name, second - value
     *
     * Tricks: pass null as first argument of array to leave command's case unchanged (no inflator calling)
     *
     * @return array
     */
    public function scenarioCommands () {
        return [];
    }

    /**
     * @return boolean
     */
    public function getIsNewRecord () {
        return !$this->getPrimaryKey();
    }
}
