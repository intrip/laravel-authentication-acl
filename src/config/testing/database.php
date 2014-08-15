<?php
return [
    /*
     * The default dbms connection used for testing
     */
    "default"     => "sqlite",

    'connections' => [

            "sqlite" => [
                    'driver'   => 'sqlite',
                    'database' => ':memory:',
                    'prefix'   => '',
            ],
            "pgsql"  => [
                    'driver'   => 'pgsql',
                    'host'     => 'localhost',
                    'database' => 'authentication_test',
                    'username' => 'root',
                    'password' => 'root',
                    'charset'  => 'utf8',
                    'prefix'   => '',
                    'schema'   => 'public'
            ],
            "mysql"  => [
                    'driver'    => 'mysql',
                    'host'      => 'localhost',
                    'database'  => 'authentication_test',
                    'username'  => 'root',
                    'password'  => 'root',
                    'charset'   => 'utf8',
                    'prefix'    => '',
                    'collation' => 'utf8_unicode_ci',
                    'schema'    => 'public'
            ]
    ]
];