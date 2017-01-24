<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use Closure;
use Yii;
use hiqdev\hiart\stream\Request;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\helpers\Json;

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
}
