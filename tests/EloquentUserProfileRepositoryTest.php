<?php  namespace Jacopo\Authentication\Tests;

use DB;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Models\UserProfile;
use Jacopo\Authentication\Repository\EloquentUserProfileRepository;

/**
 * Test EloquentUserProfileRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class EloquentUserProfileRepositoryTest extends DbTestCase
{
    protected $repo_profile;

    public function setUp()
    {
        parent::setUp();
        $this->repo_profile = new EloquentUserProfileRepository();
    }

    /**
     * @test
     **/
    public function canCreateNewProfile()
    {
        $data = $this->prepareFakeProfileData();

        $profile = $this->repo_profile->create($data);

        $this->assertInstanceOf('\Jacopo\Authentication\Models\UserProfile', $profile);
        $this->assertEquals($data['user_id'], $profile->user_id);
    }

    protected function prepareFakeProfileData($user_id = null)
    {
        return [
            'user_id' => $user_id ? $user_id : $this->faker->numberBetween(1, 100),
            'first_name' => $this->faker->firstName(), 'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }

    /**
     * @test
     **/
    public function canRetriveProfileFromUserId()
    {
        $user = $this->createFakeUser();
        list($profile_data, $profile) = $this->createFakeProfile($user);
        $profile_db = $this->repo_profile->getFromUserId($user->id);
        $this->assertEquals($profile->code, $profile_db->code);
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\UserNotFoundException
     **/
    public function it_throws_exception_if_doesnt_find_the_user()
    {
        $this->repo_profile->getFromUserId(1);
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\ProfileNotFoundException
     **/
    public function it_throws_exception_if_doesnt_find_the_profile()
    {
        $user = $this->createFakeUser();
        $this->repo_profile->getFromUserId($user->id);
    }

    protected function createFakeUser()
    {
        DB::table('users')->insert([
            "email" => $this->faker->email(),
            "password" => $this->faker->text(10), "activated" => 1,
            "created_at" => 0, "updated_at" => 0
        ]);
        return User::first();
    }

    /**
     * @test
     **/
    public function canCreateAndUserProfile()
    {
        $user = $this->createFakeUser();
        list($profile_data, $profile) = $this->createFakeProfile($user);

        $this->objectHasAllArrayAttributes($profile_data, $profile);
    }

    /**
     * @param $user
     * @return array
     */
    private function createFakeProfile($user)
    {
        $profile_data = [
            'user_id' => $user->id, 'code' => $this->faker->text('20'),
            'vat' => $this->faker->text('20'), 'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(), 'phone' => $this->faker->phoneNumber(),
            'state' => $this->faker->text(20), 'city' => $this->faker->citySuffix(),
            'country' => $this->faker->country(), 'zip' => $this->faker->numberBetween(10000, 99999),
            'address' => $this->faker->streetAddress()
        ];
        $profile = $this->repo_profile->create($profile_data);
        return array($profile_data, $profile);
    }

    /**
     * @test
     **/
    public function canCreateNewEmptyProfileIfDoesntExists()
    {
        $user = $this->createFakeUser();

        $this->repo_profile->attachEmptyProfile($user);

        $attached_profile = $this->repo_profile->getFromUserId($user->id);
        $this->assertNotEmpty($attached_profile);
    }

}
 