<?php
namespace Jacopo\Authentication\Classes\Captcha;

/**
 * Class CaptchaValidator
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
interface CaptchaValidatorInterface
{
    public function validateCaptcha($attribute, $value);

    public function getValue();

    /**
     * @return mixed
     */
    public function getErrorMessage();

    public function getImageSrcTag();
}