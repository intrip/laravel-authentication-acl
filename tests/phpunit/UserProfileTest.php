<?php  namespace Jacopo\Authentication\Tests\Unit;

use Jacopo\Authentication\Models\UserProfile;

/**
 * Test UserProfileTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserProfileTest extends DbTestCase
{

    protected $profile_model;

    public function setUp()
    {
        parent::setUp();
        $this->profile_model = new UserProfile();
    }

    /**
     * @test
     **/
    public function itEncodeProfileAvatarWithGet()
    {
        $data          = $this->createBigRandomNumber();
        $expected_data = base64_encode($data);

        $this->profile_model->avatar = $data;

        $this->assertEquals($expected_data, $this->profile_model->avatar);

    }

    /**
     * @return int
     */
    protected function createBigRandomNumber()
    {
        $data = rand(10000, 100000);
        return $data;
    }

    /**
     * @test
     **/
    public function canGetPresenter()
    {
        $profile_presenter = $this->profile_model->presenter();

        $this->assertInstanceOf('Jacopo\Authentication\Presenters\UserProfilePresenter', $profile_presenter);
    }

}
 