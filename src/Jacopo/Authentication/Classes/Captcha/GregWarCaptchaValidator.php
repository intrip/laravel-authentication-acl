<?php  namespace Jacopo\Authentication\Classes\Captcha;

use Gregwar\Captcha\CaptchaBuilder;

/**
 * Class GregWarCaptchaValidator
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class GregWarCaptchaValidator extends CaptchaValidator
{
    protected static $captcha_builder;
    protected $error_message = "The captcha is not valid, please try again.";

    public static function getInstance()
    {
        if(static::$captcha_builder) return static::$captcha_builder;

        return self::newInstance();
    }

    public static function getCaptchaBuilder()
    {
        return static::$captcha_builder;
    }

    protected static function newInstance()
    {
        static::$captcha_builder = new CaptchaBuilder();
        static::$captcha_builder->build();
    }

    public function getValue()
    {
        return static::$captcha_builder->getPhrase();
    }

    public function getImageSrcTag()
    {
        return static::$captcha_builder->inline();
    }
} 