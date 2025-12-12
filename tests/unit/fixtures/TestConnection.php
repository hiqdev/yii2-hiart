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
use hiqdev\hiart\Command;
use Psr\Http\Message\ResponseInterface;

/**
 * Mock connection for testing.
 */
class TestConnection extends AbstractConnection
{
    public function createCommand($config = []): Command
    {
        return new Command(array_merge(['db' => $this], $config));
    }

    public function getResponseError(ResponseInterface|\hiqdev\hiart\ResponseInterface $response)
    {
        // Mock implementation
        return null;
    }
}
