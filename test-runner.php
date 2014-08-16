<?php
/*
|--------------------------------------------------------------------------
| Test runner
|--------------------------------------------------------------------------
| this script is made for running all unit test under all the supported DBMS
|
*/
function bootLaravel()
{
    require __DIR__ . '/vendor/autoload.php';
    $laravel_app = require __DIR__ . '/../../../bootstrap/start.php';
    $laravel_app->boot();
}

/**
 * @param $current_connection
 * @param $config_file_path
 * @return string
 */
function replaceCurrentConnection($current_connection, $config_file_path)
{
    $connection_under_test = "'default' => '{$current_connection}'";
    $content = preg_replace(
            "/'default' => '[a-zA-Z]*'/",
            $connection_under_test,
            File::get($config_file_path)
    );

    File::put($config_file_path, $content);
    return $connection_under_test;
}

/**
 * @param $connection_under_test
 */
function printMessage($connection_under_test)
{
    echo "\n\n";
    echo "\033[32m#############################################\n";
    echo "## Running all tests with {$connection_under_test} DBMS ##\n";
    echo "#############################################\n\n";
    echo "\033[0m";
}

$connections = [
        'sqlite',
        'mysql',
        'pgsql'
];
$config_file_path = __DIR__ . '/src/config/testing/database.php';
// the default testing connection
$default_connection = 'sqlite';

bootLaravel();

foreach($connections as $current_connection)
{
    $connection_under_test = replaceCurrentConnection($current_connection, $config_file_path);
    printMessage($current_connection);
    passthru("vendor/bin/phpunit");
    $connection_under_test = replaceCurrentConnection($default_connection, $config_file_path);
}
