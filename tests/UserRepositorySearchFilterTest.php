<?php  namespace Jacopo\Authentication\Tests;
use App;
use Mockery as m;
use Jacopo\Authentication\Repository\SentryUserRepository;
use Jacopo\Authentication\Repository\UserRepositorySearchFilter;
/**
 * Test UserRepositorySearchFilterTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserRepositorySearchFilterTest extends DbTestCase {

    protected $repository_search;
    protected $user_repository;
    protected $profile_repository;
    /**
     * Results per page
     * @var int
     */
    protected $per_page = 5;

    public function setUp()
    {
        parent::setUp();

        $this->repository_search = new UserRepositorySearchFilter;
        $this->user_repository = new SentryUserRepository();
        $this->profile_repository = App::make('profile_repository');
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_gets_all_users_with_profile_paginated_and_read_from_config()
    {
        foreach (range(1,5) as $key) {
            $user_input = [
                "email" => "admin@admin.com{$key}",
                "password" => "password",
                "activated" => 1
            ];
            $user = $this->user_repository->create($user_input);
            $profile_input = [
                "first_name" => "name",
                "last_name" => "surname",
                "zip" => "22222",
                "code" => "12345",
                "user_id" => $user->id,
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
    public function it_gets_all_user_filtered_by_active_state()
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
    public function it_gets_all_user_filtered_by_first_name_last_name_zip_email_code()
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
     * @test
     **/
    public function it_ignore_empty_options_with_all()
    {
        foreach (range(1,5) as $key) {
            $input = [
                "email" => "admin@admin.com{$key}",
                "password" => "password",
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
    public function it_allow_ordering_asc_and_desc_by_first_name_last_name_email_last_login_activated_with_all()
    {
        $this->createUserWithProfileWithSameValueOnFields(0);
        $this->createUserWithProfileWithSameValueOnFields(1);

        $users = $this->repository_search->all(["order_by" => "first_name","ordering" => "asc"]);
        $this->assertEquals("0first_name", $users->first()->first_name);
        $users = $this->repository_search->all(["ordering" => "desc", "order_by" => "last_name"]);
        $this->assertEquals("1last_name", $users->first()->last_name);
        $users = $this->repository_search->all(["order_by" => "email","ordering" => "asc"]);
        $this->assertEquals($users->first()->email, "0@email.com");
        $users = $this->repository_search->all(["ordering" => "asc", "order_by" => "last_login"]);
        $this->assertEquals($users->first()->last_login, 0);
        $users = $this->repository_search->all(["ordering" => "asc", "order_by" => "activated"]);
        $this->assertEquals($users->first()->activated, 0);

    }

    /**
     * @test
     **/
    public function it_ignore_ordering_by_empty_field_with_all()
    {
        $this->createUserWithProfileWithSameValueOnFields(0);
        $this->createUserWithProfileWithSameValueOnFields(1);

        $users_ordered = $this->repository_search->all(["order_by" => ""]);
        $users = $this->repository_search->all();
        $this->assertEquals($users, $users_ordered);
    }

    /**
     * @test
     **/
    public function it_validate_ordering_filter()
    {
        $invalid_filter = ["order_by" => "invalid "];
        $this->assertFalse($this->repository_search->isValidOrderingFilter($invalid_filter));
    }

    /**
     * @test
     **/
    public function it_ignore_wrong_column_name_when_ordering_all()
    {
        $this->createUserWithProfileWithSameValueOnFields(0);
        $this->createUserWithProfileWithSameValueOnFields(1);

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
        $this->create4Active1InactiveUsers();
        $group = $this->createGroup();
        $this->user_repository->addGroup(2, $group->id);
        $this->user_repository->addGroup(3, $group->id);

        $users = $this->repository_search->all(["order_by" => "name", 'ordering' => 'desc']);

        $this->assertEquals(2, $users->first()->id);
    }

    /**
     * @param $config
     * @return SentryUserRepository
     */
    protected function createUserWithProfileForSearch()
    {
        $input        = [
            "email" => "admin@admin.com", "password" => "password", "activated" => 1];
        $user         = $this->user_repository->create($input);
        $input        = [
            "first_name" => "name", "last_name" => "surname", "zip" => "22222", "code" => "12345", "user_id" => $user->id];
        $this->profile_repository->create($input);
    }

    /**
     * @param $config
     * @return SentryUserRepository
     */
    protected function create4Active1InactiveUsers()
    {
        foreach (range(1, 5) as $key) {
            $input = [
                "email" => "admin@admin.com{$key}", "password" => "password", "activated" => ($key == 1) ? 1 : 0];
            $this->user_repository->create($input);
        }
    }

    /**
     * @param $config
     * @return array
     */
    protected function createUser()
    {
        $input_user      = [
            "email" => "admin@admin.com",
            "password" => "password",
            "activated" => 1
        ];
        $user = $this->user_repository->create($input_user);

        return $user;
    }

    /**
     * @param $repo
     */
    protected function createUserWithProfileWithSameValueOnFields($value)
    {
        $input_user = [
            "email" => "{$value}@email.com",
            "password" => "{$value}",
            "activated" => $value,
        ];
        $user = $this->user_repository->create($input_user);
        // set last login
        $this->user_repository->update($user->id,[
                                "last_login" => $value
                                ]);

        $input_profile = [
            "first_name" => "{$value}first_name",
            "last_name" => "{$value}last_name",
            "zip" => "{$value}zip",
            "code" => "{$value}code",
            "user_id" => $user->id
        ];
        $this->profile_repository->create($input_profile);
    }

    protected function createGroup($name = "group name")
    {
        $group_repository = App::make('group_repository');
        $input_group      = [
            "name" => $name,];
        $group = $group_repository->create($input_group);
        return $group;
    }
}
 