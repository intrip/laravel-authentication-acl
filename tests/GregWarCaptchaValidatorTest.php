<?php  namespace Jacopo\Authentication\Tests;
use Mockery as m;
use Jacopo\Authentication\Classes\Captcha\GregWarCaptchaValidator;

/**
 * Test GregWarCaptchaValidatorTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class GregWarCaptchaValidatorTest extends \PHPUnit_Framework_TestCase {

    protected $gregWarCaptchaValidator;

    public function setUp()
    {
        $this->gregWarCaptchaValidator = new GregWarCaptchaValidator;
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function canBeIstantiated()
    {
        $this->gregWarCaptchaValidator->getInstance();
        $captcha_creator = $this->gregWarCaptchaValidator->getCaptchaBuilder();

        $this->assertNotEmpty($captcha_creator);
        $this->assertInstanceOf('Gregwar\Captcha\CaptchaBuilder', $captcha_creator);
    }
    
    /**
     * @test
     **/
    public function itGetSameInstanceWithLazyLoading()
    {
        $this->gregWarCaptchaValidator->getInstance();
        $captcha_creator1 = $this->gregWarCaptchaValidator->getCaptchaBuilder();

        $this->gregWarCaptchaValidator->getInstance();
        $captcha_creator2 = $this->gregWarCaptchaValidator->getCaptchaBuilder();

        $this->assertSame($captcha_creator1, $captcha_creator2);
    }

    /**
     * @test
     **/
    public function canGetValue()
    {   $value1 = $this->gregWarCaptchaValidator->getValue();
        $this->assertNotEmpty($value1);
    }
    
    /**
     * @test
     **/
    public function canGetImgSrcTag()
    {
        $img_src = $this->gregWarCaptchaValidator->getImageSrcTag();
        $this->assertNotEmpty($img_src);
    }
}