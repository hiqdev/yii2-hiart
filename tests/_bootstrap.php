<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

use yii\web\Application;
use Yiisoft\Composer\Config\Builder;

error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('UTC');

require_once __DIR__ . '/../../../autoload.php';
require_once __DIR__ . '/../../../yiisoft/yii2/Yii.php';

$config = require Builder::path('web');

\Yii::setAlias('@root', dirname(__DIR__, 4));
\Yii::$app = new Application($config);
