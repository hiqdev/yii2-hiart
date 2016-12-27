<?php

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

