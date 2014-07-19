<?php  namespace Jacopo\Authentication\Tests\Unit;

use Jacopo\Authentication\Models\UserProfile;
use Jacopo\Authentication\Presenters\UserProfilePresenter;

/**
 * Test UserProfilePresenterTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserProfilePresenterTest extends TesTCase {

    /**
     * @test
     **/
    public function canGetDefaultAvatar()
    {
        $presenter = new UserProfilePresenter(new UserProfile());

        $avatar_src = $presenter->avatar_src;

        $expected_src = '/packages/jacopo/laravel-authentication-acl/images/avatar.png';
        $this->assertEquals($expected_src, $avatar_src);
    }

    /**
     * @test
     **/
    public function canGetAvatar()
    {
        $avatar_data = rand(10000,100001);
        $presenter = new UserProfilePresenter(new UserProfile(['avatar' => $avatar_data]));

        $avatar_src = $presenter->avatar_src;

        $expected_src = "data:image;base64,". base64_encode($avatar_data);
        $this->assertEquals($expected_src, $avatar_src);
    }
    
}
 