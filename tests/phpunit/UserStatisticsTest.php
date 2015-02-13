<?php  namespace Jacopo\Authentication\Tests\Unit;
use Jacopo\Authentication\Classes\Statistics\UserStatistics;
use App;
/**
 * Test UserTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserStatisticTest extends DbTestCase {

    protected $user_repository;

    protected $user_statistics;

    public function setUp()
    {
        parent::setUp();
        $this->user_repository = App::make('user_repository');
        $this->user_statistics = new UserStatistics;
    }

    /**
     * @test
     **/
    public function itGetsRegisteredUsersNumber()
    {
        $this->createMixedUsers($this->user_repository);

        $registered_users = $this->user_statistics->getRegisteredUserNumber();

        $this->assertEquals(3, $registered_users);
    }
    /**
     * @test
     **/
    public function itGetsActiveUsersNumber()
    {
        $this->createMixedUsers($this->user_repository);

        $active_users = $this->user_statistics->getActiveUserNumber();

        $this->assertEquals(2, $active_users);
    }

    /**
     * @test
     **/
    public function itGetsPendingUsersNumber()
    {
        $this->createMixedUsers($this->user_repository);

        $pending_users = $this->user_statistics->getPendingUserNumber();

        $this->assertEquals(1, $pending_users);
    }
    
    /**
     * @test
     **/
    public function itGetsBannedUsersNumber()
    {
        $this->createMixedUsers($this->user_repository);

        $banned_users = $this->user_statistics->getBannedUserNumber();

        $this->assertEquals(1, $banned_users);
    }

    private function createMixedUsers()
    {
        $active_banned_data     = [
            "email"  => $this->faker->email(), "password" => $this->faker->text(20), "activated" => 1,
            "banned" => 1
        ];
        $inactive_unbanned_data = [
            "email"  => $this->faker->email(), "password" => $this->faker->text(20), "activated" => 0,
            "banned" => 0
        ];
        $active_unbanned_data = [
            "email"  => $this->faker->email(), "password" => $this->faker->text(20), "activated" => 1,
            "banned" => 0
        ];
        $this->user_repository->create($active_banned_data);
        $this->user_repository->create($inactive_unbanned_data);
        $this->user_repository->create($active_unbanned_data);
    }
}
 