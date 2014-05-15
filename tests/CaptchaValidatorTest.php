<?php  namespace Jacopo\Authentication\Tests;

use Jacopo\Authentication\Classes\Captcha\CaptchaValidator;

/**
 * Test CaptchaValidatorTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class CaptchaValidatorTest extends TestCase {

    /**
     * @test
     **/
    public function canValidateCaptchaSuccessfully()
    {
        $captcha_value = "1224";
        $captcha_validator = new CaptchaImplementationStub($captcha_value);

        $success = $captcha_validator->validateCaptcha($captcha_value);
        $this->assertTrue($success);
    }
}

class CaptchaImplementationStub extends CaptchaValidator
{
    protected $value;

    function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}