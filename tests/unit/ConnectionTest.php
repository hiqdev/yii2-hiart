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

use hiqdev\hiart\rest\Connection;

/**
 * Connection test class.
 */
class ConnectionTest extends \PHPUnit\Framework\TestCase
{
    protected $shortUri = 'http://api.dev';

    protected $fixedUri = 'http://api.dev/';

    protected Connection $db;

    protected function setUp(): void
    {
        $this->db = new Connection();
    }

    protected function tearDown(): void
    {
    }

    public function testShortBaseUri()
    {
        $this->db->baseUri = $this->shortUri;
        $this->assertSame($this->fixedUri, $this->db->getBaseUri());
    }

    public function testFixedBaseUri()
    {
        $this->db->baseUri = $this->fixedUri;
        $this->assertSame($this->fixedUri, $this->db->getBaseUri());
    }
}
