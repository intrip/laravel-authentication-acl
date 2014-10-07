<?php  namespace Jacopo\Authentication\Tests\Unit;

use Config;
use Jacopo\Authentication\Validators\UserSignupValidator;

/**
 * Test UserSignupValidatorTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserSignupValidatorTest extends TestCase {

    protected $captcha_field;

    public function setUp()
    {
        parent::setUp();
        $this->captcha_field = "captcha_text";
    }

    /**
     * @test
     **/
    public function canReadConfigurationAndDisableCaptchaRule()
    {
        UserSignupValidatorStub::cleanCaptchaRule();
        $this->disableCaptchaCheck();
        $validator = new UserSignupValidatorStub();
        $rules = $validator->getRules();

        $this->assertFalse(array_key_exists($this->captcha_field, $rules) );
    }

    /**
     * @test
     **/
    public function canReadConfigurationAndEnableCaptchaRule()
    {
        UserSignupValidatorStub::cleanCaptchaRule();
        $this->enableCaptchaCheck();
        $validator = new UserSignupValidatorStub();
        $rules = $validator->getRules();

        $this->assertTrue(array_key_exists($this->captcha_field, $rules) );
    }

    protected function disableCaptchaCheck()
    {
        Config::set('laravel-authentication-acl::captcha_signup', false);
    }

    protected function enableCaptchaCheck()
    {
        Config::set('laravel-authentication-acl::captcha_signup', true);
    }

}

class UserSignupValidatorStub extends UserSignupValidator
{
    public static function cleanCaptchaRule()
    {
        unset(static::$rules['captcha_text']);
    }
}