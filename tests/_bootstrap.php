<?php
require __DIR__ . "/../vendor/autoload.php";

use Jacopo\Authentication\Tests\Unit\DbTestCase;

// bootstrap laravel
$unitTesting = true;
$testEnvironment = 'testing-acceptance';
$app = require __DIR__ . '/../../../../bootstrap/start.php';
$app->boot();

/*
|--------------------------------------------------------------------------
| Global functions
|--------------------------------------------------------------------------
| various helper global functions with _codeCeption prefix
|
*/

//orchestra risetta l'env in testing e non test-acceptance fai change per compatibility
// fixa il createapplication in testCase

if (!function_exists('_codeCeption_setUp')) {
  function _codeCeption_setUp($environment) {
    (new DbTestCase())->setCustomEnvironment($environment)->setUp();
  }
}

_codeCeption_setUp($testEnvironment);