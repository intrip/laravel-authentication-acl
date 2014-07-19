<?php  namespace Jacopo\Authentication\Tests\Unit;
use Illuminate\Support\Facades\Session;
use Mockery as m;
use Jacopo\Authentication\Classes\Captcha\GregWarCaptchaValidator;

/**
 * Test GregWarCaptchaValidatorTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class GregWarCaptchaValidatorTest extends TestCase {

    protected $gregWarCaptchaValidator;

    public function setUp()
    {
        parent::setUp();
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
    public function itCanBuildWithCustomParameters()
    {
        $this->resetCaptchaBuilder();
        $captcha_validator = new GregWarCaptchaValidatorBuildStub();
        $captcha_validator->getInstance();
        $this->resetCaptchaBuilder();
    }

    protected function resetCaptchaBuilder()
    {
        GregWarCaptchaValidatorBuildStub::setCaptchaBuilder(null);
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
    public function canGetValueAfterGettingImageTag()
    {
        $this->gregWarCaptchaValidator->getImageSrcTag();
        $value1 = $this->gregWarCaptchaValidator->getValue();
        $this->assertNotEmpty($value1);
        $this->assertSessionHasNoFlashData();
    }
    
    /**
     * @test
     **/
    public function canGetImgSrcTag()
    {
        $img_src = $this->gregWarCaptchaValidator->getImageSrcTag();
        $this->assertNotEmpty($img_src);
    }

    protected function assertSessionHasNoFlashData()
    {
        $all_session = Session::all();
        $this->assertFalse(isset($all_session['flash']));
    }
}

class GregWarCaptchaValidatorBuildStub extends GregWarCaptchaValidator
{
    /*
     * @override
     */
    protected static function newInstance()
    {
        static::$captcha_builder = m::mock('StdClass')
                                       ->shouldReceive('build')
                                       ->once()
                                       ->with(static::$captcha_width,
                                              static::$captcha_height)
                                       ->getMock();
        static::buildCaptcha();
    }
}