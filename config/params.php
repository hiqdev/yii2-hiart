<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

return [
    'hiart.enabled'         => true,
    'hiart.class'           => \hiqdev\hiart\rest\Connection::class,
    'hiart.requestClass'    => \hiqdev\hiart\auto\Request::class,
    'hiart.dbname'          => 'hiart',
    'hiart.auth'            => [],
    'hiart.config'          => [],
    'hiart.baseUri'         => null,
];
