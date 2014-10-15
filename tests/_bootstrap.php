<?php
require __DIR__ . "/../vendor/autoload.php";

use Jacopo\Authentication\Tests\Unit\DbTestCase;

// bootstrap laravel
$app = require __DIR__ . '/../../../../bootstrap/start.php';
$unitTesting = true;
$testEnvironment = 'testing';
$app->boot();

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