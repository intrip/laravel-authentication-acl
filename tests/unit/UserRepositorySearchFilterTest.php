<?php  namespace Jacopo\Authentication\Tests\Unit;

use App;
use Carbon\Carbon;
use Jacopo\Authentication\Repository\SentryUserRepository;
use Jacopo\Authentication\Repository\UserRepositorySearchFilter;
use Jacopo\Authentication\Tests\Unit\Traits\HourHelper;
use Jacopo\Authentication\Tests\Unit\Traits\UserFactory;
use Mockery as m;

/**
 * Test UserRepositorySearchFilterTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserRepositorySearchFilterTest extends DbTestCase
{
    use HourHelper, UserFactory;

    protected $repository_search;
    protected $user_repository;
    protected $profile_repository;
    /**
     * Results per page
     *
     * @var int
     */
    protected $per_page = 5;
    /**
     * Separator used to handle multiple ordering
     * @var String
     */
    protected $multiple_ordering_separator;

    public function setUp()
    {
        parent::setUp();
        $this->repository_search = new UserRepositorySearchFilter;
        $this->user_repository = new SentryUserRepository();
        $this->profile_repository = App::make('profile_repository');
        $this->initializeUserHasher();
        $this->multiple_ordering_separator = UserRepositorySearchFilter::$multiple_ordering_separator;
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function getsAllUsersWithProfilePaginated()
    {
        foreach(range(1, 5) as $key)
        {
            $user_input = [
                    "email" => "admin@admin.com{$key}", "password" => "password", "activated" => 1
            ];
            $user = $this->user_repository->create($user_input);
            $profile_input = [
                    "first_name" => "name", "last_name" => "surname", "zip" => "22222", "code" => "12345",
                    "user_id"    => $user->id,
            ];
            $this->profile_repository->create($profile_input);
        }
        $per_page = 5;
        $this->repository_search->setPerPage($per_page);
        $users = $this->repository_search->all();
        $this->assertInstanceOf('Illuminate\Pagination\Paginator', $users);
        $this->assertEquals($per_page, $users->count());
        $this->assertEquals($per_page, $users->getPerPage());
        $this->assertEquals("name", $users->first()->first_name);
    }

    /**
     * @test
     **/
    public function getAllUsersFilteredByActiveState()
    {
        $this->create4Active1InactiveUsers();

        $users = $this->repository_search->all(["activated" => 1]);
        $this->assertEquals(1, $users->count());

        $users = $this->repository_search->all(["activated" => 0]);
        $this->assertEquals(4, $users->count());
    }

    /**
     * @test
     **/
    public function itGetsAllUsersFilteredByBlockedState()
    {
        $this->create4Unblocked1BannedUsers();

        $users = $this->repository_search->all(["banned" => 1]);
        $this->assertEquals(1, $users->count());

        $users = $this->repository_search->all(["banned" => 0]);
        $this->assertEquals(4, $users->count());
    }

    /**
     * @param $config
     * @return SentryUserRepository
     */
    protected function create4Active1InactiveUsers()
    {
        foreach(range(1, 5) as $key)
        {
            $input = [
                    "email"     => "admin@admin.com{$key}", "password" => "password",
                    "activated" => ($key == 1) ? 1 : 0
            ];
            $this->user_repository->create($input);
        }
    }

    protected function create4Unblocked1BannedUsers()
    {
        foreach(range(1, 5) as $key)
        {
            $input = [
                    "email"     => "admin@admin.com{$key}", "password" => "password",
                    "banned"    => ($key == 1) ? 1 : 0,
                    "activated" => 1
            ];
            $this->user_repository->create($input);
        }
    }

    /**
     * @test
     **/
    public function getAllUserFilteredByFirstNameLastNameZipEmailCode()
    {
        $this->createUserWithProfileForSearch();

        $users = $this->repository_search->all(["first_name" => "name"]);
        $this->assertEquals("name", $users->first()->first_name);
        $users = $this->repository_search->all(["last_name" => "urname"]);
        $this->assertEquals("surname", $users->first()->last_name);
        $users = $this->repository_search->all(["zip" => "22222"]);
        $this->assertEquals("22222", $users->first()->zip);
        $users = $this->repository_search->all(["email" => "admin@admin.co"]);
        $this->assertEquals("admin@admin.com", $users->first()->email);
        $users = $this->repository_search->all(["code" => "12345", "email" => "admin@admin.com"]);
        $this->assertEquals("12345", $users->first()->code);
        $this->assertEquals(1, $users->first()->id);
    }

    /**
     * @param $config
     * @return SentryUserRepository
     */
    protected function createUserWithProfileForSearch()
    {
        $input = [
                "email" => "admin@admin.com", "password" => "password", "activated" => 1
        ];
        $user = $this->user_repository->create($input);
        $input = [
                "first_name" => "name", "last_name" => "surname", "zip" => "22222", "code" => "12345",
                "user_id"    => $user->id
        ];
        $this->profile_repository->create($input);
    }

    /**
     * @test
     **/
    public function ignoreEmptyOptions_WithAll()
    {
        foreach(range(1, 5) as $key)
        {
            $input = [
                    "email"     => "admin@admin.com{$key}", "password" => "password",
                    "activated" => ($key == 1) ? 1 : 0
            ];
            $this->user_repository->create($input);
        }

        $users = $this->repository_search->all(["activated" => ""]);
        $this->assertEquals(5, $users->count());
    }

    /**
     * @test
     **/
    public function allowOrderingAscDescByFirstNameLastNameEmailLastLoginActivated ()
    {
        $this->createUserProfileWithSameValueOnFields(0);
        $this->createUserProfileWithSameValueOnFields(1);

        $users = $this->repository_search->all(["order_by" => "first_name", "ordering" => "asc"]);
        $this->assertEquals("0first_name", $users->first()->first_name);
        $users = $this->repository_search->all(["ordering" => "desc", "order_by" => "last_name"]);
        $this->assertEquals("1last_name", $users->first()->last_name);
        $users = $this->repository_search->all(["order_by" => "email", "ordering" => "asc"]);
        $this->assertEquals($users->first()->email, "0@email.com");
        $users = $this->repository_search->all(["ordering" => "asc", "order_by" => "last_login"]);
        $this->assertEqualsHourFromTimestamp($users->first()->last_login, Carbon::now()->subHours(1));
        $users = $this->repository_search->all(["ordering" => "asc", "order_by" => "activated"]);
        $this->assertEquals($users->first()->activated, 0);
    }

    /**
     * @test
     **/
    public function allowOrderingByMultipleFields()
    {
        $user_1 = $this->make('Jacopo\Authentication\Models\User', $this->getUserStub())->first();
        $user_profile_1 = $this->make('Jacopo\Authentication\Models\UserProfile', array_merge($this->getUserProfileStub($user_1),["first_name" => "1", "last_name" => "2"]) )->first();
        $user_2 = $this->make('Jacopo\Authentication\Models\User', $this->getUserStub())->first();
        $user_profile_2 = $this->make('Jacopo\Authentication\Models\UserProfile', array_merge($this->getUserProfileStub($user_2),["first_name" => "1", "last_name" => "1"]) )->first();

        $users_ordered = $this->repository_search->all(["order_by" => "first_name,last_name", "ordering" => "asc,asc"]);

        $this->assertEquals($user_profile_2->last_name, $users_ordered->first()->last_name);
        $this->assertEquals($user_profile_1->last_name, $users_ordered->last()->last_name);
    }

    /**
     * @test
     **/
    public function ignoreOrderingByEmptyField()
    {
        $this->createUserProfileWithSameValueOnFields(0);
        $this->createUserProfileWithSameValueOnFields(1);

        $users_ordered = $this->repository_search->all(["order_by" => ""]);
        $users = $this->repository_search->all();
        $this->assertEquals($users, $users_ordered);
    }

    /**
     * @test
     * @group distinct
     **/
    public function itGetsDistincValues()
    {
        $this->createUserProfileWithSameValueOnFields(0);

        $users_ordered = $this->repository_search->all();
        $this->assertCount(1, $users_ordered);
    }

    /**
     * @test
     **/
    public function it_validate_ordering_filter()
    {
        $this->assertFalse($this->repository_search->isValidOrderingField("invalid"));
    }

    /**
     * @test
     **/
    public function it_ignore_wrong_column_name_when_ordering_all()
    {
        $this->createUserProfileWithSameValueOnFields(0);
        $this->createUserProfileWithSameValueOnFields(1);

        $users_ordered = $this->repository_search->all(["order_by" => "wrong_column_name"]);
        $users = $this->repository_search->all();
        $this->assertEquals($users, $users_ordered);
    }

    /**
     * @test
     **/
    public function it_filter_groups_with_all_and_select_distinct_rows()
    {
        $user = $this->createUser();
        $group1_name = "group 1";
        $group2_name = "group 2";
        $group1 = $this->createGroup($group1_name);
        $group2 = $this->createGroup($group2_name);
        $this->user_repository->addGroup($user->id, $group1->id);
        $this->user_repository->addGroup($user->id, $group2->id);

        $users = $this->repository_search->all(["group_id" => 1]);
        $this->assertEquals(1, $users->count());
        $this->assertEquals("admin@admin.com", $users->first()->email);

        $users = $this->repository_search->all(["group_id" => 3]);
        $this->assertTrue($users->isEmpty());
    }

    /**
     * @test
     **/
    public function it_order_groups_with_all()
    {
        $this->times(2)->make('Jacopo\Authentication\Models\User', function(){return $this->getUserStub();});

        $group1 = $this->createGroup("1 a first group");
        $group2 = $this->createGroup("2 a second group");
        $this->user_repository->addGroup(1, $group2->id);
        $this->user_repository->addGroup(2, $group1->id);

        $users = $this->repository_search->all([
                                                       "order_by" => "name",
                                                       'ordering' => "asc"
                                               ]);

        $this->assertEquals(2, $users->first()->id);
    }


    /**
     * @param $config
     * @return array
     */
    protected function createUser()
    {
        $input_user = [
                "email"    => "admin@admin.com",
                "password" => "password",
                "activated" => 1
        ];
        $user = $this->user_repository->create($input_user);

        return $user;
    }

    /**
     * @param $repo
     */
    protected function createUserProfileWithSameValueOnFields($value)
    {
        $input_user = [
                "email"     => "{$value}@email.com",
                "password"  => "{$value}",
                "activated" => $value,
        ];
        $user = $this->user_repository->create($input_user);
        // set last login
        $this->user_repository->update($user->id, [
                "last_login" => Carbon::now()->subHours($value)->toDateTimeString()
        ]);

        $input_profile = [
                "first_name" => "{$value}first_name",
                "last_name"  => "{$value}last_name",
                "zip"        => "{$value}zip",
                "code"       => "{$value}code",
                "user_id"    => $user->id
        ];
        $this->profile_repository->create($input_profile);

        $group1 = $this->createGroup($value."1");
        $group2 = $this->createGroup($value."2");

        $this->user_repository->addGroup($user->id, $group1->id);
        $this->user_repository->addGroup($user->id, $group2->id);
    }

    protected function createGroup($name = "group name")
    {
        $group_repository = App::make('group_repository');
        $input_group = [
                "name" => $name,
        ];
        $group = $group_repository->create($input_group);

        return $group;
    }


}
 