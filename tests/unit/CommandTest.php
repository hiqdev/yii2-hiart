<?php

/*
 * README plugin for HiDev
 *
 * @link      https://github.com/hiqdev/hidev-readme
 * @package   hidev-readme
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\tests\unit;

use hiqdev\hiart\Command;
use hiqdev\hiart\Connection;
use hiqdev\hiart\tests\Mock;
use Yii;

/**
 * Command test class.
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Command
     */
    protected $object;

    /**
     * @var Mock
     */
    protected $mock;

    protected $action  = 'testAction';
    protected $options = ['a' => 'b'];
    protected $result  = ['x' => 'z'];

    protected function setUp()
    {
        $this->mock = new Mock($this->result);
        $this->object = Yii::createObject([
            'class' => Command::className(),
            'index' => 'test',
            'db'    => $this->mock,
        ]);
    }

    protected function tearDown()
    {
    }

    public function testSearch()
    {
        $result = $this->object->search($this->options);
        $this->assertSame($this->result, $result);
        $this->assertSame('post', $this->mock->name);
        $this->assertSame('testSearch', $this->mock->args[0]);
        $this->assertSame($this->options, $this->mock->args[1]);
    }

    public function testPerform()
    {
        $result = $this->object->perform($this->action, $this->options);
        $this->assertSame($this->result, $result);
        $this->assertSame('post', $this->mock->name);
        $this->assertSame($this->action, $this->mock->args[0]);
        $this->assertSame($this->options, $this->mock->args[1]);
    }
}
