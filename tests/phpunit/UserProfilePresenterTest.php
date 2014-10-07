<?php  namespace Jacopo\Authentication\Tests\Unit;

use Config;
use Jacopo\Authentication\Presenters\UserProfilePresenter;
use Jacopo\Authentication\Tests\Unit\Traits\UserFactory;

/**
 * Test UserProfilePresenterTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserProfilePresenterTest extends DbTestCase
{
    use UserFactory;

    protected $user_profile;
    protected $faker;
    protected $user;
    protected $presenter;
    protected $default_path = '/fake/path/file.jpg';
    protected $user_email;
    protected $expected_gravatar;

    public function setUp()
    {
        parent::setUp();
        $this->setAvatarDefaultPath();
        $this->useGravatar(true);
        $this->faker = \Faker\Factory::create();
        $this->initializeUserWithProfile();
        $this->presenter = new UserProfilePresenter($this->user_profile);
        $this->user_profile->avatar = rand(10000, 100001);
        $this->getExpectedAvatar(30);
    }

    /**
     * @test
     **/
    public function canGetDefaultAvatar()
    {
        $this->user_profile->avatar = null;
        $this->assertEquals($this->default_path, $this->presenter->custom_avatar);
    }

    /**
     * @test
     **/
    public function canGetAvatar()
    {
        $expected_avatar = "data:image/png;base64," . $this->user_profile->avatar;
        $this->assertEquals($expected_avatar, $this->presenter->custom_avatar);
    }

    /**
     * @test
     **/
    public function canGetGravatar()
    {
        $this->assertEqualsAvatar($this->getExpectedAvatar(30), 'gravatar');
    }

    /**
     * @test
     **/
    public function canGetAvatarOfGivenSize()
    {
        $size = 300;

        $this->assertEqualsAvatar($this->getExpectedAvatar($size), 'gravatar', 300);
    }

    /**
     * @test
     **/
    public function canGetAvatarOrGravatarDependingOnConfiguration()
    {
        $this->useGravatar(true);

        $expected_gravatar = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $this->user_email ) ) ) . "?d=" . urlencode( $this->default_path ) . "&s=" . 30;

        $this->assertEqualsAvatar($this->getExpectedAvatar(30), 'avatar');

        $this->useGravatar(false);

        $expected_avatar = "data:image/png;base64," . $this->user_profile->avatar;
        $this->assertEquals($expected_avatar, $this->presenter->custom_avatar);
    }

    private function initializeUserWithProfile()
    {
        $this->user = $this->initializeUserHasher()->make('Jacopo\Authentication\Models\User', $this->getUserStub())->first();
        $this->user_profile = $this->make('Jacopo\Authentication\Models\UserProfile', $this->getUserProfileStub($this->user))->first();
    }

    private function setAvatarDefaultPath()
    {
        Config::set('laravel-authentication-acl::config.default_avatar_path', $this->default_path);
    }

    /**
     * @param $expected_gravatar
     */
    private function assertEqualsAvatar($expected_gravatar, $field_name, $params = 30)
    {
        $this->assertEquals(substr($expected_gravatar, 0, 31), substr($this->presenter->$field_name($params), 0, 31));
        $this->assertEquals(substr($expected_gravatar, 63), substr($this->presenter->$field_name($params), 63));
    }

    private function useGravatar($use)
    {
        Config::set('laravel-authentication-acl::config.use_gravatar', $use);
    }

    /**
     * @return string
     */
    private function getExpectedAvatar($size)
    {
        return $this->expected_gravatar = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($this->user_email))) . "?s=" . $size;
    }
}
 