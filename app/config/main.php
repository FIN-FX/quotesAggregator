<?php

$config = [
    'id' => 'agg',
    'bootstrap' => ['log'],
    'basePath' => dirname(__DIR__),
    'components' => [
        'request' => ['class' => "app\\components\\Request"],
        'daemon' => [
            'class' => 'app\components\Server',
            'handler' => [
                'class' => 'app\components\MainHandler',
                'assets' => ['BTC', 'ETH', 'BNB'],
                'workers' => [
                    'ws' => [
                        'class' => 'app\handlers\WebSocketHandler',
                        'worker' => [
                            'class' => 'app\workers\WebSocketWorker',
                            'config' => [
                                'type' => 'ws',
                                'port' => 4000,
                            ],
                        ],
                    ],
                ],
            ]
        ],
        'connectionsManager' => [
            'class' => 'app\components\ConnectionsManager',
        ],
        'threadsHandler' => [
            'class' => 'app\\components\\ThreadsHandler',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1,
            'targets' => [
                'error' => [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => 'php://stdout',
                    'levels' => ['error'],
                    'logVars' => [],
                    'exportInterval' => 1,
                ],
            ]
        ],
    ],
    'params' => [

    ]
];

return $config;