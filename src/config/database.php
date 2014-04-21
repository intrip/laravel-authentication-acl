<?php
return array(
    /*
    |--------------------------------------------------------------------------
    | Connection name
    |--------------------------------------------------------------------------
    |
    | Set the connection name to use, if you want to use your application default
    | connection leave the flag as 'default'. Otherwise write the connection
    | name: for example 'mysql'.
    |
    */
    'default' => 'default',

    'connections' => array(

        'sqlite' => array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ),

        'mysql' => array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'database',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),

        'pgsql2' => array(
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'database' => 'database',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ),

        'sqlsrv' => array(
            'driver'   => 'sqlsrv',
            'host'     => 'localhost',
            'database' => 'database',
            'username' => 'root',
            'password' => '',
            'prefix'   => '',
        ),

    ),
);
