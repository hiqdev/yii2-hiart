<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\db\ActiveQueryInterface;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

class ActiveRecord extends BaseActiveRecord
{
    /**
     * Returns the database connection used by this AR class.
     * By default, the "hiart" application component is used as the database connection.
     * You may override this method if you want to use a different database connection.
     *
     * @return AbstractConnection the database connection used by this AR class
     */
    public static function getDb()
    {
        return AbstractConnection::getDb();
    }

    /**
     * {@inheritdoc}
     * @return ActiveQuery the newly created [[ActiveQuery]] instance
     */
    public static function find()
    {
        $class = static::getDb()->activeQueryClass;

        return new $class(get_called_class());
    }

    public function isScenarioDefault()
    {
        return $this->scenario === static::SCENARIO_DEFAULT;
    }

    /**
     * Gets a record by its primary key.
     *
     * @param mixed $primaryKey the primaryKey value
     * @param array $options    options given in this parameter are passed to API
     *
     * @return null|static the record instance or null if it was not found
     */
    public static function get($primaryKey = null, $options = [])
    {
        if ($primaryKey === null) {
            return null;
        }
        $command = static::getDb()->createCommand();
        $result  = $command->get(static::tableName(), $primaryKey, $options);

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
     * The primaryKey for HiArt objects is the `id` field by default. This field is not part of the
     * ActiveRecord attributes so you should never add `_id` to the list of [[attributes()|attributes]].
     *
     * You may override this method to define the primary key name.
     *
     * Note that HiArt only supports _one_ attribute to be the primary key. However to match the signature
     * of the [[\yii\db\ActiveRecordInterface|ActiveRecordInterface]] this methods returns an array instead of a
     * single string.
     *
     * @return string[] array of primary key attributes. Only the first element of the array will be used.
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * Returns the list of attribute names.
     * By default, this method returns all attributes mentioned in rules.
     * You may override this method to change the default behavior.
     * @return string[] list of attribute names
     */
    public function attributes()
    {
        $attributes = [];
        foreach ($this->rules() as $rule) {
            $names = reset($rule);
            if (is_string($names)) {
                $names = [$names];
            }
            foreach ($names as $name) {
                if (substr_compare($name, '!', 0, 1) === 0) {
                    $name = mb_substr($name, 1);
                }
                $attributes[$name] = $name;
            }
        }

        return array_values($attributes);
    }

    /**
     * Creates an active record instance.
     *
     * This method is called together with [[populateRecord()]] by [[ActiveQuery]].
     * It is not meant to be used for creating new records directly.
     *
     * You may override this method if the instance being created
     * depends on the row data to be populated into the record.
     * For example, by creating a record based on the value of a column,
     * you may implement the so-called single-table inheritance mapping.
     *
     * @return static the newly created active record
     */
    public static function instantiate($row)
    {
        return new static();
    }

    /**
     * @return string the name of the entity of this record
     */
    public static function tableName()
    {
        return Inflector::camel2id(StringHelper::basename(get_called_class()), '-');
    }

    /**
     * Declares the name of the model associated with this class.
     * By default this method returns the class name by calling [[Inflector::camel2id()]].
     *
     * @return string the module name
     */
    public static function modelName()
    {
        return Inflector::camel2id(StringHelper::basename(get_called_class()));
    }

    public function insert($runValidation = true, $attributes = null, $options = [])
    {
        if ($runValidation && !$this->validate($attributes)) {
            return false;
        }

        if (!$this->beforeSave(true)) {
            return false;
        }

        $values = $this->getDirtyAttributes($attributes);
        $data   = array_merge($values, $options, ['id' => $this->getOldPrimaryKey()]);
        $result = $this->performScenario('insert', $data);

        $pk        = static::primaryKey()[0];
        $this->$pk = $result['id'];
        if ($pk !== 'id') {
            $values[$pk] = $result['id'];
        }
        $changedAttributes = array_fill_keys(array_keys($values), null);
        $this->setOldAttributes($values);
        $this->afterSave(true, $changedAttributes);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($options = [])
    {
        if (!$this->beforeDelete()) {
            return false;
        }

        $data   = array_merge($options, ['id' => $this->getOldPrimaryKey()]);
        $result = $this->performScenario('delete', $data);

        $this->setOldAttributes(null);
        $this->afterDelete();

        return $result === false ? false : true;
    }

    public function update($runValidation = true, $attributeNames = null, $options = [])
    {
        if ($runValidation && !$this->validate($attributeNames)) {
            return false;
        }

        return $this->updateInternal($attributeNames, $options);
    }

    protected function updateInternal($attributes = null, $options = [])
    {
        if (!$this->beforeSave(false)) {
            return false;
        }

        $values = $this->getAttributes($attributes);
        if (empty($values)) {
            $this->afterSave(false, $values);

            return 0;
        }

        $result = $this->performScenario('update', $values, $options);

        $changedAttributes = [];
        foreach ($values as $name => $value) {
            $changedAttributes[$name] = $this->getOldAttribute($name);
            $this->setOldAttribute($name, $value);
        }

        $this->afterSave(false, $changedAttributes);

        return $result === false ? false : true;
    }

    public function performScenario($defaultScenario, $data, array $options = [])
    {
        $action = $this->getScenarioAction($defaultScenario);

        return static::perform($action, $data, $options);
    }

    public static function perform($action, $data, array $options = [])
    {
        return static::getDb()->createCommand()->perform($action, static::tableName(), $data, $options);
    }

    /**
     * Converts scenario name to action.
     * @param string $default default action name
     * @throws InvalidConfigException
     * @throws NotSupportedException
     * @return string
     */
    public function getScenarioAction($default = '')
    {
        if ($this->isScenarioDefault()) {
            if ($default !== '') {
                $result = Inflector::id2camel($default);
            } else {
                throw new InvalidConfigException('Scenario not specified');
            }
        } else {
            $scenarioCommands = static::scenarioCommands();
            if ($action = $scenarioCommands[$this->scenario]) {
                if ($action === false) {
                    throw new NotSupportedException('The scenario can not be saved');
                }

                if (is_array($action) && $action[0] === null) {
                    $result = $action[1];
                } elseif (is_array($action)) {
                    $result = $action;
                } else {
                    $result = Inflector::id2camel($action);
                }
            } else {
                $result = Inflector::id2camel($this->scenario);
            }
        }

        return is_array($result) ? implode('', $result) : $result;
    }

    /**
     * Define an array of relations between scenario and API call action.
     *
     * Example:
     *
     * ```
     * [
     *      'update-name'                => 'set-name', /// ModuleSetName
     *      'update-related-name'        => [Action::formName(), 'SetName'], /// ActionSetName
     *      'update-self-case-sensetive' => [null, 'SomeSENSETIVE'] /// ModuleSomeSENSETIVE
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
    public function scenarioCommands()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function getIsNewRecord()
    {
        return !$this->getPrimaryKey();
    }

    /**
     * This method has no effect in HiArt ActiveRecord.
     */
    public function optimisticLock()
    {
        return null;
    }

    /**
     * Destroys the relationship in current model.
     *
     * This method is not supported by HiArt.
     */
    public function unlinkAll($name, $delete = false)
    {
        throw new NotSupportedException('unlinkAll() is not supported by HiArt, use unlink() instead.');
    }

    /**
     * {@inheritdoc}
     *
     * @return ActiveQueryInterface|ActiveQuery the relational query object. If the relation does not exist
     *                                          and `$throwException` is false, null will be returned.
     */
    public function getRelation($name, $throwException = true)
    {
        return parent::getRelation($name, $throwException);
    }

    /**
     * {@inheritdoc}
     * @return ActiveQuery the relational query object
     */
    public function hasOne($class, $link)
    {
        return parent::hasOne($class, $link);
    }

    /**
     * {@inheritdoc}
     * @return ActiveQuery the relational query object
     */
    public function hasMany($class, $link)
    {
        return parent::hasMany($class, $link);
    }
}
