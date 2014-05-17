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
    protected static $captcha_width = 150;
    protected static $captcha_height = 40;

    public static function getInstance()
    {
        if(static::$captcha_builder) return static::$captcha_builder;

        return static::newInstance();
    }

    public static function getCaptchaBuilder()
    {
        return static::$captcha_builder;
    }

    /**
     * @param mixed $captcha_builder
     */
    public static function setCaptchaBuilder($captcha_builder)
    {
        self::$captcha_builder = $captcha_builder;
    }

    protected static function newInstance()
    {
        static::$captcha_builder = new CaptchaBuilder();
        static::buildCaptcha();

        return static::$captcha_builder;
    }

    protected static function buildCaptcha()
    {
        static::getInstance()->build(static::$captcha_width, static::$captcha_height);
    }

    public function getValue()
    {
        return static::getInstance()->getPhrase();
    }

    public function getImageSrcTag()
    {
        return static::getInstance()->inline();
    }
} 