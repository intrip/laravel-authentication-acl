<?php  namespace Jacopo\Authentication\Models;

/**
 * Test UserProfileTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserProfileTest extends \PHPUnit_Framework_TestCase
{

    protected $profile_model;

    public function setUp()
    {
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

}
 