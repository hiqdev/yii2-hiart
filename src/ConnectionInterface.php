<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

/**
 * HiArt connection interface.
 */
interface ConnectionInterface
{
    /**
     * Gets connection by name or finds default.
     * @param string $dbname
     * @return ConnectionInterface
     */
    public static function getDb($dbname = null);

    /**
     * Creates API command.
     * @param array $config
     * @return Command response
     */
    public function createCommand(array $config = []);
}
