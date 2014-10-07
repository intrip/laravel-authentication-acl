<?php  namespace Jacopo\Authentication\Tests\Unit;

use Carbon\Carbon;
use DB;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Models\UserProfile;
use Jacopo\Authentication\Repository\EloquentUserProfileRepository;
use Jacopo\Authentication\Tests\Unit\Traits\UserFactory;

/**
 * Test EloquentUserProfileRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class EloquentUserProfileRepositoryTest extends DbTestCase
{
    use UserFactory;

    protected $repo_profile;

    public function setUp()
    {
        parent::setUp();
        $this->initializeUserHasher();
        $this->repo_profile = new EloquentUserProfileRepository();
    }

    /**
     * @test
     **/
    public function canCreateNewProfile()
    {
        $users = $this->make('Jacopo\Authentication\Models\User', $this->getUserStub());
        $data = $this->prepareFakeProfileData($users[0]->id);
        $profile = $this->repo_profile->create($data);

        $this->assertInstanceOf('\Jacopo\Authentication\Models\UserProfile', $profile);
        $this->assertEquals($data['user_id'], $profile->user_id);
    }

    protected function prepareFakeProfileData($user_id = null)
    {
        return [
                'user_id'    => $user_id ? $user_id : $this->faker->numberBetween(1, 100),
                'first_name' => $this->faker->firstName(), 'last_name' => $this->faker->lastName(),
                'phone'      => $this->faker->phoneNumber(),
        ];
    }

    /**
     * @test
     **/
    public function canRetriveProfileFromUserId()
    {
        $users = $this->make('Jacopo\Authentication\Models\User', $this->getUserStub());
        list($profile_data, $profile) = $this->createFakeProfile($users[0]);
        $profile_db = $this->repo_profile->getFromUserId($users[0]->id);
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
        $users = $this->make('Jacopo\Authentication\Models\User', $this->getUserStub());
        $this->repo_profile->getFromUserId($users[0]->id);
    }

    /**
     * @test
     **/
    public function canCreateAndUserProfile()
    {
        $users = $this->make('Jacopo\Authentication\Models\User', $this->getUserStub());
        list($profile_data, $profile) = $this->createFakeProfile($users[0]);

        $this->objectHasAllArrayAttributes($profile_data, $profile);
    }

    /**
     * @param $user
     * @return array
     */
    private function createFakeProfile($user)
    {
        $profile_data = [
                'user_id'    => $user->id, 'code' => $this->faker->text('20'),
                'vat'        => $this->faker->text('20'),
                'first_name' => $this->faker->firstName(),
                'last_name'  => $this->faker->lastName(),
                'phone'      => $this->faker->phoneNumber(),
                'state'      => $this->faker->text(20),
                'city'       => $this->faker->citySuffix(),
                'country'    => $this->faker->country(),
                'zip'        => $this->faker->numberBetween(10000, 99999),
                'address'    => $this->faker->streetAddress()
        ];
        $profile = $this->repo_profile->create($profile_data);
        return array($profile_data, $profile);
    }

    /**
     * @test
     **/
    public function canCreateNewEmptyProfile()
    {
        $user = $this->make('Jacopo\Authentication\Models\User', $this->getUserStub())->first();

        $this->repo_profile->attachEmptyProfile($user);

        $attached_profile = $this->repo_profile->getFromUserId($user->id);
        $this->assertNotEmpty($attached_profile);
    }

    /**
     * @test
     **/
    public function attachNewProfileOnlyIfDoesNotExists()
    {
        $user = $this->make('Jacopo\Authentication\Models\User', $this->getUserStub())->first();

        $this->repo_profile->attachEmptyProfile($user);
        $this->repo_profile->attachEmptyProfile($user);

        $this->assertEquals(1, UserProfile::get()->count());
    }
}
 