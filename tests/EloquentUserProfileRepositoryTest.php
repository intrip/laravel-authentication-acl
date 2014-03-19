<?php  namespace Jacopo\Authentication\Tests;

use Jacopo\Authentication\Repository\EloquentUserProfileRepository;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Models\UserProfile;
use DB;
/**
 * Test EloquentUserProfileRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class EloquentUserProfileRepositoryTest extends DbTestCase {

    protected $faker;

    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();
    }

    /**
     * @test
     **/
    public function it_create_a_new_profile()
    {
        $repo = new EloquentUserProfileRepository();

        $data = $this->prepareFakeProfileData();
        $profile = $repo->create($data);

        $this->assertInstanceOf('\Jacopo\Authentication\Models\UserProfile', $profile);
        $this->assertEquals($data['user_id'], $profile->user_id);
    }

    /**
     * @test
     **/
    public function it_retrive_profile_from_a_user_id()
    {
        $repo = new EloquentUserProfileRepository();
        // setup fake data
        $user = $this->prepareFakeUser();
        $profile = $this->prepareFakeProfile($user->id);
        $profile_db = $repo->getFromUserId($user->id);
        $this->assertEquals($profile->code, $profile_db->code);
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\UserNotFoundException
     **/
    public function it_throws_exception_if_doesnt_find_the_user()
    {
        $repo = new EloquentUserProfileRepository();
        $repo->getFromUserId(1);
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\ProfileNotFoundException
     **/
    public function it_throws_exception_if_doesnt_find_the_profile()
    {
        $user = $this->prepareFakeUser();
        $repo = new EloquentUserProfileRepository();
        $repo->getFromUserId($user->id);
    }

    protected function prepareFakeProfileData($user_id = null)
    {
        return [
                'user_id' => $user_id ? $user_id : $this->faker->numberBetween(1, 100),
                'first_name' => $this->faker->firstName(),
                'last_name' => $this->faker->lastName(),
                'phone' => $this->faker->phoneNumber(),
            ];
    }

    protected function prepareFakeProfile($user_id = null)
    {
        return UserProfile::create($this->prepareFakeProfileData($user_id));
    }

    protected function prepareFakeUser()
    {
        DB::table('users')->insert(
                            [
                             "email" => $this->faker->email(),
                             "password" => $this->faker->text(10),
                             "activated" => 1,
                             "created_at" => 0,
                             "updated_at" => 0
                             ]);
        return User::first();
    }

}
 