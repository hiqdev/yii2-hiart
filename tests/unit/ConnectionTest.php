<?php

/*
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\tests\unit;

use GuzzleHttp\Psr7\Response;
use hiqdev\hiart\Connection;
use hiqdev\hiart\tests\Mock;
use Yii;

/**
 * Connection test class.
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected $object;

    /**
     * @var Mock
     */
    protected $mock;

    protected $site   = 'http://test.api.site/';
    protected $url    = 'test/url';
    protected $query  = ['a' => 'b'];
    protected $body   = ['c' => 'd'];
    protected $result = 'xyz result string';

    protected function setUp()
    {
        $response     = new Response(200, [], $this->result);
        $this->mock   = new Mock($response);
        $this->object = Yii::createObject([
            'class'   => Connection::class,
            'handler' => $this->mock,
            'config'  => [
                'base_uri' => $this->site,
            ],
            'errorChecker' => function ($res) {
                return null;
            },
        ]);
    }

    protected function tearDown()
    {
    }

    public function testGet()
    {
        $result = $this->object->get($this->url, [], $this->body, false);
        $this->assertSame($this->result, $result);
        $this->assertSame('request',   $this->mock->name);
        $this->assertSame('GET',       $this->mock->args[0]);
        $this->assertSame($this->url,  $this->mock->args[1]);
        $this->assertSame($this->body, $this->mock->args[2]['form_params']);
    }

    public function testErrorChecker()
    {
        $this->object->setErrorChecker(function ($res) { return $res; });
        $this->setExpectedException('hiqdev\hiart\ErrorResponseException', $this->result);
        $this->object->get($this->url, [], $this->body, false);
    }
}
