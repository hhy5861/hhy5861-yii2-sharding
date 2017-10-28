<?php
Yii::setAlias('@orders', __DIR__ . '/../modules/orders');

$config = [
    'language' => 'zh-CN',
    'basePath' => __DIR__ . '/../',
    'vendorPath' => __DIR__ . '/../vendor',
    'runtimePath' => __DIR__ . '/../runtime',
    'modules' => [
        'orders' => [
            'class' => 'orders\Module',
        ],
    ],

    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'app\components\base\FileTarget',
                    'logVars' => [],
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/error.log',
                    'maxFileSize' => 51200,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => 'app\components\base\FileTarget',
                    'logVars' => [],
                    'levels' => ['info'],
                    'except' => ['yii\*'],
                    'logFile' => '@runtime/logs/info.log',
                    'maxFileSize' => 51200,
                    'maxLogFiles' => 10,
                ],
            ],
        ],

        /*'cache' => [
            'class' => 'yii\redis\Cache',
        ],*/

        'orders' => [
            'class' => 'app\components\shard\Connection',
            'nodes' => $yaml['NODES'],
            'dbShardKey' => $yaml['NODES']['node_1']['dbShardKey'],
            'tableShardKey' => $yaml['NODES']['node_1']['tableShardKey'],
            'tablePrefix' => $yaml['NODES']['node_1']['prefix'],
            'dsn' => sprintf(
                'mysql:host=%s;port=%d;dbname=%s',
                $yaml['NODES']['node_1']['host'],
                $yaml['NODES']['node_1']['port'],
                $yaml['NODES']['node_1']['database']
            ),
            'username' => $yaml['NODES']['node_1']['username'],
            'password' => $yaml['NODES']['node_1']['password'],

            'slaveConfig' => [
                'username' => $yaml['NODES']['node_1']['slave']['username'],
                'password' => $yaml['NODES']['node_1']['slave']['username'],
                'attributes' => [
                    PDO::ATTR_TIMEOUT => 10,
                ],
            ],

            'slaves' => [
                [
                    'dsn' => sprintf(
                        'mysql:host=%s;port=%d;dbname=%s',
                        $yaml['NODES']['node_1']['slave']['dsn'][0]['host'],
                        $yaml['NODES']['node_1']['slave']['dsn'][0]['port'],
                        $yaml['NODES']['node_1']['slave']['dsn'][0]['database']
                    )
                ],
                [
                    'dsn' => sprintf(
                        'mysql:host=%s;port=%d;dbname=%s',
                        $yaml['NODES']['node_1']['slave']['dsn'][1]['host'],
                        $yaml['NODES']['node_1']['slave']['dsn'][1]['port'],
                        $yaml['NODES']['node_1']['slave']['dsn'][1]['database']
                    )
                ],
                array(
                    'dsn' => sprintf(
                        'mysql:host=%s;port=%d;dbname=%s',
                        $yaml['NODES']['node_1']['slave']['dsn'][2]['host'],
                        $yaml['NODES']['node_1']['slave']['dsn'][2]['port'],
                        $yaml['NODES']['node_1']['slave']['dsn'][2]['database']
                    )
                )
            ],
        ],

        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => $yaml['DB_REDIS']['host'],
            'port' => $yaml['DB_REDIS']['port'],
            'password' => $yaml['DB_REDIS']['auth'] ?: null,
            'database' => 0,
        ],

        'memcache' => [
            'class' => 'yii\caching\MemCache',
            'servers' => [
                [
                    'host' => $yaml['DB_MEMCACHE']['default']['host'],
                    'port' => $yaml['DB_MEMCACHE']['default']['port'],
                    'weight' => 60,
                ],
            ],
        ],

        'verificationCode' => [
            'class' => 'app\components\utils\VerificationCode',
        ],

        'client' => [
            'class' => 'mike\client\Client',
            'remotes' => [
                'user' => [$yaml['URL_HTTP_API']['user']],
            ],

            'on beforeSend' => function (\mike\client\Request $request) {
                /* @var \mike\zipkin\Tracer $tracer */
                if ($tracer = Yii::$app->get('__tracer__', false)) {
                    $span = $tracer->createSpan(sprintf('%s:%s', $request->getRemote(), $request->getPath()));
                    $request->setHeaders(\mike\zipkin\Headers::createFromSapn($span)->toArray());
                    $get = $request->getQuery();
                    $span->addBinaryAnnotation('GET', $get ? http_build_query($get) : '');
                    $post = $request->getJson() ? $request->getJson() : $request->getFormParams();
                    $span->addBinaryAnnotation('POST', $post ? http_build_query($post) : '');
                    $span->start()->clientSend();
                    $request->setData('span', $span);
                }
            },

            'on afterRecv' => function (\mike\client\response\ResponseInterface $response) {
                /* @var \mike\zipkin\Span $span */
                if ($span = $response->getRequest()->getData('span')) {
                    $span->addBinaryAnnotation('RESPONSE', $response->getBody());
                    $span->clientRecv()->finish();
                }
            },
        ],
    ],
];

if (YII_DEBUG) {
    $config['components']['log']['targets'] = \yii\helpers\ArrayHelper::merge($config['components']['log']['targets'], [
        [
            'class' => 'app\components\base\FileTarget',
            'logVars' => [],
            'levels' => ['trace'],
            'logFile' => '@runtime/logs/trace.log',
            'maxFileSize' => 1024,
            'maxLogFiles' => 5,
        ],
        [
            'class' => 'app\components\base\FileTarget',
            'logVars' => [],
            'levels' => ['profile'],
            'logFile' => '@runtime/logs/profile.log',
            'maxFileSize' => 1024,
            'maxLogFiles' => 5,
        ],
    ]);
}

if (\yii\helpers\ArrayHelper::isIn(YII_ENV, ['local'])) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'mike\gii\Module',
    ];
}

return $config;
