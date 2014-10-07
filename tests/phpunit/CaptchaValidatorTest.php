<?php  namespace Jacopo\Authentication\Tests\Unit;
use Jacopo\Authentication\Classes\Captcha\CaptchaValidator;
use Validator;
/**
 * Test CaptchaValidatorTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class CaptchaValidatorTest extends TestCase {

    protected $captcha_validator;

    public function setUp()
    {
        parent::setUp();
        $this->captcha_validator = new CaptchaImplementationStub();
    }

    /**
     * @test
     **/
    public function canValidateCaptchaSuccessfully()
    {
        $captcha_value = "captcha value";
        $this->captcha_validator->value = $captcha_value;

        $success = $this->captcha_validator->validateCaptcha('useless info', $captcha_value);

        $this->assertTrue($success);
    }

    /**
     * @test
     **/
    public function canValidateInputSuccesfullyAsLaravelValidator()
    {
        $captcha_value = "captcha value";
        $input = ["captcha_input" => $captcha_value];
        $rules = ["captcha_input" => "captcha"];
        Validator::extend('captcha', 'Jacopo\Authentication\Tests\Unit\CaptchaImplementationStub@validateCaptcha');

        $laravel_validator = Validator::make($input, $rules);

        $this->assertFalse($laravel_validator->fails());
    }

    /**
     * @test
     **/
    public function canValidateInputErrorsAsLaravelValidator()
    {
        $captcha_value = "captcha value wrong";
        $input = ["captcha_input" => $captcha_value];
        $rules = ["captcha_input" => "captcha"];
        Validator::extend('captcha', 'Jacopo\Authentication\Tests\Unit\CaptchaImplementationStub@validateCaptcha', $this->captcha_validator->getErrorMessage());

        $laravel_validator = Validator::make($input, $rules);

        $this->assertTrue($laravel_validator->fails() );
        $this->assertEquals($this->captcha_validator->getErrorMessage(), $laravel_validator->messages()->first('captcha_input') );
    }
}

class CaptchaImplementationStub extends CaptchaValidator
{
    public $value = "captcha value";

    public function getValue()
    {
        return $this->value;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getImageSrcTag()
    {}
}