<?php  namespace Jacopo\Authentication\Classes\Captcha;

use Gregwar\Captcha\CaptchaBuilder;
use Session;
/**
 * Class GregWarCaptchaValidator
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class GregWarCaptchaValidator extends CaptchaValidator
{
    protected static $captcha_builder;
    protected static $captcha_width = 280;
    protected static $captcha_height = 60;

    protected $captcha_field;

    public function __construct() { $this->captcha_field = 'authentication_captcha_value'; }

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
        return Session::get($this->captcha_field);
    }

    public function getImageSrcTag()
    {
        $captcha_builder = static::getInstance();
        $this->saveCaptchaValue($captcha_builder);

        return $captcha_builder->inline();
    }

    /**
     * @param $captcha_builder
     */
    protected function saveCaptchaValue($captcha_builder)
    {
        Session::put($this->captcha_field, $captcha_builder->getPhrase());
    }
} 