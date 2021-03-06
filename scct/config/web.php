<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'scct',
    'defaultRoute' => 'login',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
	'on beforeRequest' => function ($event) {
        if(!Yii::$app->request->isSecureConnection){
            // add some filter/exemptions if needed ..
            $url = Yii::$app->request->getAbsoluteUrl();
            if (!strpos($url,"local")) {
                $url = str_replace('http://', 'https://', $url);
                Yii::$app->getResponse()->redirect($url);
                Yii::$app->end();
            }
        }
    },
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'cEUWDCC6m0pi4PLRSMMjEbXUhgn0704m',
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'trace'],
                ],
            ],
        ],
        //'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
			'class' => 'yii\web\UrlManager',
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'enableStrictParsing' => false,
			'rules' => [
				// your rules go here
			],
		]
    ],
	'modules' => [
        'dispatch' => [
            'class' => 'app\modules\dispatch\Module',
        ],
		'gridview' =>  [
			'class' => '\kartik\grid\Module'
			// enter optional module parameters below - only if you need to  
			// use your own export download action or custom translation 
			// message source
			// 'downloadAction' => 'gridview/export/download',
			// 'i18n' => []
		]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
		'allowedIPs' => ['127.0.0.1', '::1', '192.168.*.*'],
    ];
}

return $config;
