<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

return [
    'hiart.enabled'         => true,
    'hiart.class'           => \hiqdev\hiart\rest\Connection::class,
    'hiart.requestClass'    => \hiqdev\hiart\stream\Request::class,
    'hiart.dbname'          => 'hiart',
    'hiart.config'          => [],
    'hiart.baseUri'         => null,
];
