<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

return [
    'modules' => array_filter([
        'debug' => defined('YII_DEBUG') && YII_DEBUG ? [
            'panels' => [
                'hiart' => [
                    'class' => \hiqdev\hiart\DebugPanel::class,
                ],
            ],
        ] : null,
    ]),
];
