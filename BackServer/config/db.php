<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=mysql-master;dbname=my_database',
    'driverName' => 'mysql',
    'username' => 'my_user',
    'password' => '123',
    'charset' => 'utf8mb4',

    'slaveConfig' => [
        'username' => 'my_user',
        'password' => '123',
        'charset' => 'utf8mb4',
        'driverName' => 'mysql',
    ],

    'slaves'=>[
        ['dsn' => 'mysql:host=mysql-slave;dbname=my_database']
    ],
];
