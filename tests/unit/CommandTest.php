<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\tests\unit;

use hiqdev\hiart\Command;
use Yii;

/**
 * Command test class.
 */
class CommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Command
     */
    protected $command;

    protected $table = 'testAction';
    protected $columns = ['a' => 'b'];

    protected function setUp()
    {
        $this->command = Yii::createObject([
            'class' => Command::class,
            'db'    => Yii::$app->get('hiart'),
        ]);
    }

    protected function tearDown()
    {
    }

    public function testInsert()
    {
        $this->command->insert($this->table, $this->columns);
    }
}
