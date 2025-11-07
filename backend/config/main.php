<?php

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],

    'as access' => [
        'class' => \yii\filters\AccessControl::class,
        'rules' => [
            // allow guests to open login + error
            [
                'allow' => true,
                'actions' => ['login', 'error', 'captcha'],
                'roles' => ['?'], // guests
            ],
            // everything else only for admins
            [
                'allow' => true,
                'roles' => ['admin'],
            ],
        ],
        'denyCallback' => function () {
            throw new \yii\web\ForbiddenHttpException('Доступ запрещён');
        },
    ],

    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => \common\models\User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,   // pretty URLs without ?r=
            'showScriptName' => false,   // hide index.php
            'rules' => [
                '' => 'site/index',
                'login'  => 'site/login',
                'logout' => 'site/logout',

                // optional handy rules for posts:
                'post' => 'post/index',
                'post/create' => 'post/create',
                'post/<id:\d+>' => 'post/view',
                'post/update/<id:\d+>' => 'post/update',
            ],
        ],
    ],
    
    'params' => require __DIR__ . '/params.php',
];
