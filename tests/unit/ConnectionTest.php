<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\tests\unit;

use hiqdev\hiart\rest\Connection;

/**
 * Connection test class.
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    protected $shortUri = 'http://api.dev';

    protected $fixedUri = 'http://api.dev/';

    protected function setUp()
    {
        $this->db = new Connection();
    }

    protected function tearDown()
    {
    }

    public function testGetBaseUri()
    {
        $this->db->baseUri = $this->shortUri;
        $this->assertSame($this->fixedUri, $this->db->getBaseUri());
        $this->db->baseUri = $this->fixedUri;
        $this->assertSame($this->fixedUri, $this->db->getBaseUri());
    }
}
