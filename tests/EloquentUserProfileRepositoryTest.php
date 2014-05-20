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

  protected $faker;
  protected $repo_profile;

  public function setUp()
  {
    parent::setUp();
    $this->repo_profile = new EloquentUserProfileRepository();
    $this->faker        = \Faker\Factory::create();
  }

  /**
   * @test
   **/
  public function it_create_a_new_profile()
  {

    $data    = $this->prepareFakeProfileData();
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
  public function it_retrive_profile_from_a_user_id()
  {
    // setup fake data
    $user       = $this->prepareFakeUser();
    $profile    = $this->prepareFakeProfile($user->id);
    $profile_db = $this->repo_profile->getFromUserId($user->id);
    $this->assertEquals($profile->code, $profile_db->code);
  }

  protected function prepareFakeUser()
  {
    DB::table('users')->insert([
                               "email"      => $this->faker->email(),
                               "password"   => $this->faker->text(10), "activated" => 1,
                               "created_at" => 0, "updated_at" => 0
                               ]);
    return User::first();
  }

  protected function prepareFakeProfile($user_id = null)
  {
    return UserProfile::create($this->prepareFakeProfileData($user_id));
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
    $user = $this->prepareFakeUser();
    $this->repo_profile->getFromUserId($user->id);
  }

  /**
   * @test
   **/
  public function canCreateAnUserProfile()
  {
    $user = $this->prepareFakeUser();
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
      'user_id'    => $user->id, 'code' => $this->faker->text('20'),
      'vat'        => $this->faker->text('20'), 'first_name' => $this->faker->firstName(),
      'last_name'  => $this->faker->lastName(), 'phone' => $this->faker->phoneNumber(),
      'state'      => $this->faker->text(20), 'city' => $this->faker->citySuffix(),
      'country'    => $this->faker->country(), 'zip' => $this->faker->numberBetween(10000, 99999),
      'address'    => $this->faker->streetAddress()
    ];
    $profile      = $this->repo_profile->create($profile_data);
    return array($profile_data, $profile);
  }

  /**
   * @test
   **/
  public function itUpdateNewAvatar()
  {
      $user = $this->prepareFakeUser();
      list($profile_data, $profile) = $this->createFakeProfile($user);

//      $this->repo_profile->updateAvatar();
      //@todo stub test e usa il imageTraitHelper e poi fai integ test:
      // http://stackoverflow.com/questions/15813508/symfony-functionnal-tests-receive-uploaded-file
  }

}
 