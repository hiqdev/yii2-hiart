<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

return empty($params['hiart.enabled']) ? [] : [
    'app' => [
        'modules' => array_filter([
            'debug' => empty($params['debug.enabled']) ? null : [
                'panels' => [
                    'hiart' => [
                        'class' => \hiqdev\hiart\debug\DebugPanel::class,
                    ],
                ],
            ],
        ]),
    ],

    \hiqdev\hiart\ConnectionInterface::class => \yii\di\Reference::to($params['hiart.dbname']),
    $params['hiart.dbname'] => array_filter([
        'class'         => $params['hiart.class'],
        'requestClass'  => $params['hiart.requestClass'],
        'name'          => $params['hiart.dbname'],
        'auth'          => $params['hiart.auth'],
        'config'        => $params['hiart.config'],
        'baseUri'       => $params['hiart.baseUri'],
    ]),
];
