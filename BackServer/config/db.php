<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=master;dbname=laravel',
    'username' => 'root',
    'password' => '123',
    'charset' => 'utf8',

    'slaveConfig' => [
        'username' => 'root',
        'password' => '123',
    ],

    'slaves'=>[
        'dsn' => 'mysql:host=slave;dbname=laravel'
    ],

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
