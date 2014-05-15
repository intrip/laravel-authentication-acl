<?php  namespace Jacopo\Authentication\Classes\Captcha; 
/**
 * Class CaptchaValidator
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
abstract class CaptchaValidator implements CaptchaValidatorInterface
{
    protected $error_message;

    public function validateCaptcha($value)
    {
        return $value == $this->getValue();
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    abstract public function getValue();

    abstract public function getImageSrcTag();
}