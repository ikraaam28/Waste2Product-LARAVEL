<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    */

    'default' => env('DB_CONNECTION', 'mongodb'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */

    'connections' => [

        'mongodb' => [
            'driver'   => 'mongodb',
            'dsn'      => env('MONGODB_DSN', 'mongodb://127.0.0.1:27017'),
            'database' => env('MONGODB_DATABASE', 'waste2product'),
            'options'  => array_filter([
                'username' => env('MONGODB_USERNAME'),
                'password' => env('MONGODB_PASSWORD'),
            ], function ($value) {
                return !is_null($value) && $value !== '';
            }),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

];
