<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Testing Connection name
    |--------------------------------------------------------------------------
    |
    */
    'default' => 'sqlitedb',

    'connections' => [

            'sqlitedb' => array(
                    'driver'   => 'sqlite',
                    'database' => __DIR__.'/../../database/testing-acceptance.sqlite',
                    'prefix'   => '',
            ),

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
