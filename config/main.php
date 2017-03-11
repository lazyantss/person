<?php
$params = array_merge(
    require(__DIR__ . '/params.static.conf.php'),
    require(__DIR__ . '/params.dynamic.conf.php')
);
return [
    'id' => 'oauth',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],

    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCsrfValidation' => false,
            'enableCookieValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'identityCookie'    => array(
                'name'          => 'oauth_session',
            ),
            'idParam'           => 'oauth_session',
        ],
        'errorHandler' => [
            'errorAction' => 'oauth/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1,  // default is 1000
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning','info'],
                    'categories' => ['system.*'],
                    'logVars' => [],
                    'logFile'=>  $params['log_path'] . date($params['formatDate']) .'.log',
                    'exportInterval' => 1,   // default is 1000
                ],
                [
                    'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
					'categories' => [
					    'yii\db\*',
					    'yii\web\HttpException:*',
					],
					'except' => [
					    'yii\web\HttpException:404',
					],
                    'logVars' => [],
                    'logFile'=>  $params['log_path'] . date($params['formatDate']) .'.log',
                    'exportInterval' => 1,   // default is 1000
                ],
            ],
        ],
        'Fmt' => [
            'class' => 'app\models\Fmt',
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [],
        ],
    ],
    'aliases' => [
        '@OAuth2' => '@vendor/oauth2/src/OAuth2',
        '@Predis' => '@vendor/Predis/src',
    ],
    'params' => $params,
];


