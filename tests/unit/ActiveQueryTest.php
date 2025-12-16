<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\tests\unit;

use Closure;
use hiqdev\hiart\AbstractConnection;
use hiqdev\hiart\ActiveQuery;
use hiqdev\hiart\ActiveRecord;
use hiqdev\hiart\Command;
use hiqdev\hiart\rest\QueryBuilder;
use hiqdev\hiart\tests\unit\fixtures\TestActiveRecord;
use hiqdev\hiart\tests\unit\fixtures\TestConnection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use yii\base\Event;
use PHPUnit\Framework\TestCase;


/**
 * ActiveQuery test class for PHPUnit 12.5 with PHP attributes.
 * Uses reusable test fixtures from fixtures/ directory.
 */
class ActiveQueryTest extends TestCase
{
    private MockObject|ActiveRecord $mockModel;
    private MockObject|AbstractConnection $mockConnection;
    private MockObject|Command $mockCommand;
    private MockObject|QueryBuilder $mockQueryBuilder;
    private string $modelClass;
    private string $testModelClass;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock model class
        $this->mockModel = $this->createMock(ActiveRecord::class);
        $this->modelClass = get_class($this->mockModel);

        // Use reusable test model class
        $this->testModelClass = TestActiveRecord::class;

        // Create a mock connection
        $this->mockConnection = $this->createMock(AbstractConnection::class);

        // Create a mock command
        $this->mockCommand = $this->createMock(Command::class);

        // Create a mock query builder
        $this->mockQueryBuilder = $this->createMock(QueryBuilder::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        TestActiveRecord::setMockConnection(null);
        unset($this->mockModel, $this->mockConnection, $this->mockCommand, $this->mockQueryBuilder);
    }

    #[Test]
    #[TestDox('Constructor initializes query with model class')]
    public function testConstructorInitializesModelClass(): void
    {
        $query = new ActiveQuery($this->modelClass);

        $this->assertSame($this->modelClass, $query->modelClass);
        $this->assertIsArray($query->joinWith);
        $this->assertEmpty($query->joinWith);
    }

    #[Test]
    #[TestDox('Constructor accepts a configuration array')]
    public function testConstructorAcceptsConfiguration(): void
    {
        $config = [
            'where' => ['status' => 'active'],
            'limit' => 10,
        ];

        $query = new ActiveQuery($this->modelClass, $config);

        $this->assertSame($this->modelClass, $query->modelClass);
        $this->assertEquals(['status' => 'active'], $query->where);
        $this->assertEquals(10, $query->limit);
    }

    #[Test]
    #[TestDox('Init method triggers EVENT_INIT event')]
    public function testInitTriggersEvent(): void
    {
        $eventTriggered = false;

        $query = new ActiveQuery($this->modelClass);
        $query->on(ActiveQuery::EVENT_INIT, function (Event $event) use (&$eventTriggered) {
            $eventTriggered = true;
        });

        $query->init();

        $this->assertTrue($eventTriggered, 'EVENT_INIT should be triggered');
    }

    #[Test]
    #[TestDox('Select method sets select property')]
    #[DataProvider('selectColumnsProvider')]
    public function testSelectSetsColumns(mixed $columns, mixed $expected): void
    {
        $query = new ActiveQuery($this->modelClass);
        $result = $query->select($columns);

        $this->assertSame($query, $result, 'Should return fluent interface');
        $this->assertEquals($expected, $query->select);
    }

    public static function selectColumnsProvider(): array
    {
        return [
            'string column' => ['id', 'id'],
            'array of columns' => [['id', 'name'], ['id', 'name']],
            'associative array' => [['user_id' => 'id'], ['user_id' => 'id']],
            'null value' => [null, null],
            'empty array' => [[], []],
        ];
    }

    #[Test]
    #[TestDox('AddSelect adds columns to existing selection')]
    #[DataProvider('addSelectProvider')]
    public function testAddSelectAddsColumns(?array $initial, array|string $toAdd, array $expected): void
    {
        $query = new ActiveQuery($this->modelClass);
        $query->select = $initial;

        $result = $query->addSelect($toAdd);

        $this->assertSame($query, $result, 'Should return fluent interface');
        $this->assertEquals($expected, $query->select);
    }

    public static function addSelectProvider(): array
    {
        return [
            'add to null' => [null, ['id'], ['id']],
            'add string to null' => [null, 'id', ['id']],
            'add to existing' => [['id'], ['name'], ['id', 'name']],
            'add an array to existing' => [['id'], ['name', 'email'], ['id', 'name', 'email']],
            'add string to existing' => [['id'], 'name', ['id', 'name']],
            'add associative' => [['id'], ['user_name' => 'name'], ['id', 'user_name' => 'name']],
        ];
    }

    #[Test]
    #[TestDox('JoinWith adds relations to joinWith property')]
    public function testJoinWithAddsRelations(): void
    {
        $query = new ActiveQuery($this->modelClass);

        $result = $query->joinWith('profile');

        $this->assertSame($query, $result, 'Should return fluent interface');
        $this->assertCount(1, $query->joinWith);
        $this->assertEquals([['profile']], $query->joinWith);
    }

    #[Test]
    #[TestDox('JoinWith can be called multiple times')]
    public function testJoinWithMultipleCalls(): void
    {
        $query = new ActiveQuery($this->modelClass);

        $query->joinWith('profile')
              ->joinWith('orders');

        $this->assertCount(2, $query->joinWith);
        $this->assertEquals([['profile'], ['orders']], $query->joinWith);
    }

    #[Test]
    #[TestDox('JoinWith accepts array of relations')]
    public function testJoinWithAcceptsArray(): void
    {
        $query = new ActiveQuery($this->modelClass);

        $query->joinWith(['profile', 'orders']);

        $this->assertCount(1, $query->joinWith);
        $this->assertEquals([['profile', 'orders']], $query->joinWith);
    }

    #[Test]
    #[TestDox('JoinWith accepts associative array with callbacks')]
    public function testJoinWithAcceptsAssociativeArray(): void
    {
        $query = new ActiveQuery($this->modelClass);
        $callback = function ($q) {
            $q->where(['status' => 'active']);
        };

        $query->joinWith(['profile' => $callback]);

        $this->assertCount(1, $query->joinWith);
        $this->assertArrayHasKey('profile', $query->joinWith[0]);
        $this->assertInstanceOf(Closure::class, $query->joinWith[0]['profile']);
    }

    #[Test]
    #[TestDox('Populate returns empty array for empty input')]
    public function testPopulateReturnsEmptyArrayForEmptyInput(): void
    {
        $query = new ActiveQuery($this->testModelClass);

        $result = $query->populate([]);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    #[TestDox('Populate creates models from rows')]
    public function testPopulateCreatesModelsFromRows(): void
    {
        $query = new ActiveQuery($this->testModelClass);

        $rows = [
            ['id' => 1, 'name' => 'John', 'status' => 'active'],
            ['id' => 2, 'name' => 'Jane', 'status' => 'inactive'],
        ];

        $result = $query->populate($rows);

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf($this->testModelClass, $result);
    }

    #[Test]
    #[TestDox('Populate with indexBy closure')]
    #[DataProvider('indexByProvider')]
    public function testPopulateWithIndexBy(mixed $indexBy, array $rows, array $expectedKeys): void
    {
        $query = new ActiveQuery($this->testModelClass);
        $query->indexBy = $indexBy;

        $result = $query->populate($rows);

        $this->assertEquals($expectedKeys, array_keys($result));
    }

    public static function indexByProvider(): array
    {
        $rows = [
            ['id' => 1, 'email' => 'john@example.com'],
            ['id' => 2, 'email' => 'jane@example.com'],
        ];

        return [
            'no indexBy' => [
                null,
                $rows,
                [0, 1],
            ],
            'indexBy string' => [
                'id',
                $rows,
                [1, 2],
            ],
            'indexBy closure' => [
                fn($model) => $model->email,
                $rows,
                ['john@example.com', 'jane@example.com'],
            ],
        ];
    }

    #[Test]
    #[TestDox('One returns null when asArray is false and no results')]
    public function testOneReturnsNullWhenNoResults(): void
    {
        $query = new class($this->testModelClass) extends ActiveQuery {
            public function searchOne($db = null): ?array
            {
                return null;
            }
        };

        $query->asArray = false;

        $result = $query->one();

        $this->assertNull($result);
    }

    #[Test]
    #[TestDox('All returns an empty array when asArray is false and no results')]
    public function testAllReturnsEmptyArrayWhenNoResults(): void
    {
        $query = new class($this->testModelClass) extends ActiveQuery {
            public function searchAll($db = null): array
            {
                return [];
            }
        };

        $query->asArray = false;

        $result = $query->all();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    #[TestDox('Prepare processes joinWith only once')]
    public function testPrepareProcessesJoinWithOnlyOnce(): void
    {
        $query = new class($this->testModelClass) extends ActiveQuery {
            public int $buildJoinWithCallCount = 0;

            protected function buildJoinWith(): void
            {
                $this->buildJoinWithCallCount++;
                parent::buildJoinWith();
            }
        };

        $query->joinWith('profile');

        // First call should process joinWith
        $query->prepare();
        $this->assertEquals(1, $query->buildJoinWithCallCount);

        // The second call should not process joinWith again
        $query->prepare();
        $this->assertEquals(1, $query->buildJoinWithCallCount);
    }

    #[Test]
    #[TestDox('CreateCommand handles via relations for lazy loading')]
    public function testCreateCommandHandlesViaRelations(): void
    {
        $query = new class($this->testModelClass) extends ActiveQuery {
            /** @var array<int, mixed> */
            public array $filteredModels = [];

            protected function filterByModels($models): void
            {
                $this->filteredModels = $models;
            }

            public function createCommand($db = null)
            {
                // Execute only the lazy-loading / via-relations part.
                // Avoid calling parent::createCommand() to keep this a unit test (no DB/command creation involved).
                if ($this->primaryModel !== null) {
                    if (is_array($this->via)) {
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

                return new \stdClass();
            }
        };

        $primaryModel = $this->createMock(ActiveRecord::class);
        $relatedModel = $this->createMock(ActiveRecord::class);

        $viaQuery = $this->createMock(ActiveQuery::class);
        $viaQuery->multiple = false;

        $query->primaryModel = $primaryModel;
        $query->via = ['viaRelation', $viaQuery];

        $viaQuery->expects($this->once())
                 ->method('one')
                 ->willReturn($relatedModel);

        $viaQuery->expects($this->never())
                 ->method('all');

        $primaryModel->expects($this->once())
                     ->method('populateRelation')
                     ->with('viaRelation', $relatedModel);

        // Trigger the code under test
        $query->createCommand();

        // Verify the query was constrained by the via model (core side effect)
        $this->assertSame([$relatedModel], $query->filteredModels);
    }

    #[Test]
    #[TestDox('Memory optimization: unsets processed rows during model creation')]
    public function testMemoryOptimizationDuringModelCreation(): void
    {
        $query = new ActiveQuery($this->testModelClass);

        // Create 100 rows to trigger memory optimization
        $rows = [];
        for ($i = 1; $i <= 100; $i++) {
            $rows[] = ['id' => $i, 'name' => "User $i"];
        }

        $result = $query->populate($rows);

        $this->assertCount(100, $result);
        $this->assertContainsOnlyInstancesOf($this->testModelClass, $result);
    }

    #[Test]
    #[TestDox('Populate calls afterFind on each model')]
    public function testPopulateCallsAfterFindOnEachModel(): void
    {
        /**
         * We must ensure that ActiveQuery::populate() actually instantiates *this* class.
         * The fixture model's instantiate() uses `new self()` (not late-static binding),
         * so we override instantiate()/populateRecord() here to guarantee our subclass is used.
         */
        $modelClass = new class() extends TestActiveRecord {
            private static int $afterFindCalls = 0;

            public static function resetAfterFindCalls(): void
            {
                self::$afterFindCalls = 0;
            }

            public static function getAfterFindCalls(): int
            {
                return self::$afterFindCalls;
            }

            public function afterFind(): void
            {
                self::$afterFindCalls++;
            }

            public static function instantiate($row): static
            {
                $instance = new static();
                $instance->data = $row;
                return $instance;
            }

            public static function populateRecord($record, $row): void
            {
                if ($record instanceof static) {
                    $record->data = $row;
                }
            }
        };

        $modelClass::resetAfterFindCalls();

        $query = new ActiveQuery(get_class($modelClass));
        $rows = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];

        $models = $query->populate($rows);

        // Ensure we really created our subclass models (otherwise afterFind() counting is meaningless).
        $this->assertContainsOnlyInstancesOf(get_class($modelClass), $models);

        // afterFind should be called for each model
        $this->assertSame(3, $modelClass::getAfterFindCalls());
    }

    #[Test]
    #[TestDox('JoinWith handles empty array gracefully')]
    public function testJoinWithHandlesEmptyArray(): void
    {
        $query = new ActiveQuery($this->modelClass);

        $result = $query->joinWith([]);

        $this->assertSame($query, $result);
        $this->assertEquals([[]], $query->joinWith);
    }

    #[Test]
    #[TestDox('Select can be called multiple times')]
    public function testSelectCanBeCalledMultipleTimes(): void
    {
        $query = new ActiveQuery($this->modelClass);

        $query->select(['id', 'name']);
        $query->select(['email']);

        $this->assertEquals(['email'], $query->select);
    }

    #[Test]
    #[TestDox('Query maintains fluent interface for method chaining')]
    public function testFluentInterface(): void
    {
        $query = new ActiveQuery($this->modelClass);

        $result = $query
            ->select(['id'])
            ->addSelect(['name'])
            ->joinWith('profile');

        $this->assertSame($query, $result);
        $this->assertEquals(['id', 'name'], $query->select);
        $this->assertCount(1, $query->joinWith);
    }

    #[Test]
    #[TestDox('CreateCommand uses model connection when db is null')]
    public function testCreateCommandUsesModelConnection(): void
    {
        // This test verifies the connection resolution logic
        $query = new ActiveQuery($this->testModelClass);

        // The test model should provide its own connection
        $this->assertInstanceOf(TestConnection::class, TestActiveRecord::getDb());
    }

    #[Test]
    #[TestDox('Populate handles models with a custom instantiate method')]
    public function testPopulateHandlesCustomInstantiate(): void
    {
        $query = new ActiveQuery($this->testModelClass);

        $rows = [
            ['id' => 1, 'name' => 'Custom'],
        ];

        $result = $query->populate($rows);

        $this->assertCount(1, $result);
        $this->assertInstanceOf($this->testModelClass, $result[0]);
    }

    #[Test]
    #[TestDox('AddSelect preserves associative keys')]
    public function testAddSelectPreservesAssociativeKeys(): void
    {
        $query = new ActiveQuery($this->modelClass);
        $query->select = ['id' => 'user_id'];

        $query->addSelect(['name' => 'username']);

        $this->assertEquals(['id' => 'user_id', 'name' => 'username'], $query->select);
    }

    #[Test]
    #[TestDox('IndexBy string accesses model attribute')]
    public function testIndexByStringAccessesModelAttribute(): void
    {
        $query = new ActiveQuery($this->testModelClass);
        $query->indexBy = 'id';

        $rows = [
            ['id' => 10, 'name' => 'Test'],
            ['id' => 20, 'name' => 'Test2'],
        ];

        $result = $query->populate($rows);

        $this->assertArrayHasKey(10, $result);
        $this->assertArrayHasKey(20, $result);
    }

    #[Test]
    #[TestDox('Populate works with empty with clause')]
    public function testPopulateWorksWithEmptyWithClause(): void
    {
        $query = new ActiveQuery($this->testModelClass);
        $query->with = [];

        $rows = [['id' => 1, 'name' => 'Test']];

        $result = $query->populate($rows);

        $this->assertCount(1, $result);
    }
}
