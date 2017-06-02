<?php
/**
 * ActiveRecord for API.
 *
 * @see      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

return empty($params['hiart.enabled']) ? [] : [
    'modules' => array_filter([
        'debug' => empty($params['debug.enabled']) ? null : [
            'panels' => [
                'httpclient' => [
                    'class' => \yii\httpclient\debug\HttpClientPanel::class,
                ],
            ],
        ],
    ]),
    'components' => array_filter([
        $params['hiart.dbname'] => array_filter([
            'class'         => $params['hiart.class'],
            'auth'          => $params['hiart.auth'],
            'config'        => $params['hiart.config'],
            'baseUri'       => $params['hiart.baseUri'],
        ]),
    ]),
];
