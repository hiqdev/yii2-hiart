<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\tests\unit\fixtures;

use hiqdev\hiart\AbstractConnection;
use hiqdev\hiart\ActiveQuery;
use hiqdev\hiart\ActiveRecord;

/**
 * Test ActiveRecord model with mock connection.
 */
class TestActiveRecord extends ActiveRecord
{
    private static ?AbstractConnection $mockConnection = null;
    private array $data = [];

    public static function tableName(): string
    {
        return 'test_table';
    }

    public static function instantiate($row): self
    {
        $instance = new self();
        $instance->data = $row;
        return $instance;
    }

    public static function populateRecord($record, $row): void
    {
        if ($record instanceof self) {
            $record->data = $row;
        }
    }

    public function __get($name)
    {
        return $this->data[$name] ?? parent::__get($name);
    }

    public function __set($name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function afterFind(): void
    {
        // Hook for testing
    }

    public static function getDb(): AbstractConnection
    {
        if (self::$mockConnection === null) {
            self::$mockConnection = new TestConnection();
        }
        return self::$mockConnection;
    }

    public static function setMockConnection(?AbstractConnection $connection): void
    {
        self::$mockConnection = $connection;
    }

    public function attributes(): array
    {
        return ['id', 'name', 'email', 'status'];
    }

    public function hasAttribute($name): bool
    {
        return in_array($name, $this->attributes(), true);
    }

    public function getAttribute($name)
    {
        return $this->data[$name] ?? null;
    }

    public function setAttribute($name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function getRelation($name, $throwException = true): ActiveQuery
    {
        // Return a mock relation for testing
        return new ActiveQuery(self::class);
    }
}
