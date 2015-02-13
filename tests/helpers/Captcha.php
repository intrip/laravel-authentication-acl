<?php namespace Jacopo\Authentication\Codeception\Helpers;
use App;

class Captcha {

  protected $captcha;

  public function __construct() {
    $this->captcha = App::make('captcha_validator');
  }

  public function getValue()
  {
    return $this->captcha->getValue();
  }
}