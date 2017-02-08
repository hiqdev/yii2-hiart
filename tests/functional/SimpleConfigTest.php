<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\tests\functional;

use hiqdev\hiart\curl\Request;
use hiqdev\hiart\rest\Connection;
use Yii;
use yii\console\Application;

class SimpleConfigTest extends \PHPUnit\Framework\TestCase
{
    protected $simpleConfig = [
        'id' => 'hiart-simple-config',
        'basePath' => __DIR__,
        'components' => [
            'hiart' => [
                'class' => Connection::class,
                'requestClass' => Request::class,
                'baseUri' => 'https://site.com/api/v3/',
            ],
        ],
    ];

    private $app;
    private $container;

    public function setUp()
    {
        $this->app = Yii::$app;
        $this->container = Yii::$container;
        Yii::$app = new Application($this->simpleConfig);
        Yii::$container = new yii\di\Container();
    }

    public function tearDown()
    {
        Yii::$app = $this->app;
        Yii::$container = $this->container;
    }

    public function testGetDb()
    {
        $db = Connection::getDb();
        $this->assertInstanceOf(Connection::class, $db);
        $this->assertSame(Request::class, $db->requestClass);
    }
}
