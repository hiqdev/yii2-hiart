<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

use yii\console\Application;

error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('UTC');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$params = require __DIR__ . '/../config/params.php';
$config = require __DIR__ . '/../config/common.php';

\Yii::$app = new Application(array_merge([
    'id' => 'hiart-tests',
    'basePath' => dirname(__DIR__),
    'params' => $params,
], $config));
