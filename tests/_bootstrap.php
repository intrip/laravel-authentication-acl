<?php
require __DIR__ . "/../vendor/autoload.php";

use Jacopo\Authentication\Tests\Unit\DbTestCase;

// bootstrap laravel
$app = require __DIR__ . '/../../../../bootstrap/start.php';
$unitTesting = true;
$testEnvironment = 'testing-acceptance';
$app->boot();

//@todo here set the correct environment then move from file sqlite to memory also here
dd($app->environment());

/*
|--------------------------------------------------------------------------
| Global functions
|--------------------------------------------------------------------------
| various helper global functions with _codeCeption prefix
|
*/

if (!function_exists('_codeCeption_setUp')) {
  function _codeCeption_setUp() {
    (new DbTestCase())->setUp();
  }
}

_codeCeption_setUp();