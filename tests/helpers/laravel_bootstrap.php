<?php
use Jacopo\Authentication\Tests\Unit\DbTestCase;

$unitTesting = true;
$app = require __DIR__ . '/../../../../../bootstrap/start.php';
$app->boot();

/*
|--------------------------------------------------------------------------
| Global functions
|--------------------------------------------------------------------------
| various helper global functions with _codeCeption prefix
|
*/

if (!function_exists('_codeCeption_setUp')) {
  function _codeCeption_setUp($environment) {
    (new DbTestCase())->setCustomEnvironment($environment)->setUp();
  }
}