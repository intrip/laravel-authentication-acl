<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Testing Connection name
    |--------------------------------------------------------------------------
    |
    */
    'default' => 'sqlite',

    'connections' => [

            'sqlite' => [
                    'driver'   => 'sqlite',
                    'database' => ':memory:',
                    'prefix'   => '',
            ],

            'mysql' => [
                    'driver'    => 'mysql',
                    'host'      => 'localhost',
                    'database'  => 'authentication_test',
                    'username'  => 'root',
                    'password'  => 'root',
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix'    => '',
            ],

            'pgsql' => [
                    'driver'   => 'pgsql',
                    'host'     => 'localhost',
                    'database' => 'authentication_test',
                    'username' => 'root',
                    'password' => 'root',
                    'charset'  => 'utf8',
                    'prefix'   => '',
                    'schema'   => 'public',
            ],
    ],
];
