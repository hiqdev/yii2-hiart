<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

use Yiisoft\Composer\Config\Builder;
use yii\web\Application;

error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('UTC');
$config = require Builder::path('web');

require_once __DIR__ . '/../../../autoload.php';
require_once __DIR__ . '/../../../yiisoft/yii2/Yii.php';


\Yii::setAlias('@root', dirname(__DIR__, 4));
\Yii::$app = new Application($config);
