<?php  namespace Jacopo\Authentication\Tests;
use App, Config;
use Jacopo\Authentication\Repository\SentryUserRepository;
use Mockery as m;
use Cartalyst\Sentry\Users\UserExistsException;
/**
 * Test SentryUserRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class SentryUserRepositoryTest extends DbTestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_find_user_from_a_group()
    {
        $repo = new SentryUserRepository();
        $input = [
            "email" => "admin@admin.com",
            "password" => "password",
            "activated" => 1
        ];
        $repo->create($input);
        $group_repo = App::make('group_repository');
        $input = [
            "name" => "admin"
        ];
        $group_repo->create($input);

        $repo->addGroup(1, 1);
        $users = $repo->findFromGroupName('admin');
        $this->assertEquals("admin@admin.com", $users[0]->email);
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\UserExistsException
     **/
    public function it_throws_exception_if_user_aready_exists()
    {
        $mock_sentry = m::mock('StdClass');
        $mock_sentry->shouldReceive('createUser')
            ->andThrow(new UserExistsException)
            ->getMock();
        App::instance('sentry', $mock_sentry);
        $repo = new SentryUserRepository();
        $repo->create([
                          "email" => "email",
                          "password" => "password",
                          "activated" => "activated",
                      ]);
    }

    /**
     * @test
     **/
    public function it_activate_a_user()
    {
        $repo = new SentryUserRepository();
        $input = [
            "email" => "admin@admin.com",
            "password" => "password",
            "activated" => 0,
            "activation_code" => "code"
        ];
        $repo->create($input);

        $repo->activate("admin@admin.com");

        $user = $repo->find(1);
        $this->assertTrue($user->activated);
        $this->assertNotEmpty($user->activated_at);
        $this->assertNull($user->activation_code);
    }

    /**
     * @test
     **/
    public function it_find_user_by_login_name()
    {
        $repo = new SentryUserRepository();
        $input = [
            "email" => "admin@admin.com",
            "password" => "password",
            "activated" => 0
        ];
        $repo->create($input);

        $user = $repo->findByLogin("admin@admin.com");
        $this->assertEquals("admin@admin.com", $user->email);
    }

    /**
     * @test
     * @expectedException Jacopo\Authentication\Exceptions\UserNotFoundException
     **/
    public function it_throws_exception_if_cannot_find_user_by_login()
    {
        $repo = new SentryUserRepository();
        $user = $repo->findByLogin("admin@admin.com");
        $this->assertEquals("admin@admin.com", $user->email);
    }
    
    /**
     * @test
     **/
    public function it_gets_all_users_with_profile_paginated_and_read_from_config()
    {
        $per_page = 5;
        $config = $this->mockConfigPerPage($per_page);
        $repo = new SentryUserRepository($config);
        foreach (range(1,5) as $key) {
            $input = [
                "email" => "admin@admin.com{$key}",
                "password" => "password",
                "activated" => 1
            ];
            $user = $repo->create($input);
            $repo_profile =  App::make('profile_repository');
            $input = [
                "first_name" => "name",
                "last_name" => "surname",
                "zip" => "22222",
                "code" => "12345",
                "user_id" => $user->id,
            ];
            $repo_profile->create($input);
        }

        $users = $repo->all();
        $this->assertInstanceOf('Illuminate\Pagination\Paginator', $users);
        $this->assertEquals(5, $users->count());
        $this->assertEquals($per_page, $users->getPerPage());
        $this->assertEquals("name", $users->first()->first_name);
    }

    /**
     * @test
     **/
    public function it_gets_all_user_filtered_by_active_state()
    {
        $per_page = 5;
        $config = $this->mockConfigPerPage($per_page);
        $repo = $this->create4Active1InactiveUsers($config);

        $users = $repo->all(["activated" => 1]);
        $this->assertEquals(1, $users->count());

        $users = $repo->all(["activated" => 0]);
        $this->assertEquals(4, $users->count());
    }

    /**
     * @test
     **/
    public function it_gets_all_user_filtered_by_first_name_last_name_zip_email_code()
    {
        $per_page = 5;
        $config = $this->mockConfigPerPage($per_page);
        $repository = $this->createUserWithProfileForSearch($config);

        $users = $repository->all(["first_name" => "name"]);
        $this->assertEquals("name", $users->first()->first_name);
        $users = $repository->all(["last_name" => "urname"]);
        $this->assertEquals("surname", $users->first()->last_name);
        $users = $repository->all(["zip" => "22222"]);
        $this->assertEquals("22222", $users->first()->zip);
        $users = $repository->all(["email" => "admin@admin.co"]);
        $this->assertEquals("admin@admin.com", $users->first()->email);
        $users = $repository->all(["code" => "12345", "email" => "admin@admin.com"]);
        $this->assertEquals("12345", $users->first()->code);
        $this->assertEquals(1, $users->first()->id);
    }

    /**
     * @test
     **/
    public function it_ignore_empty_options_with_all()
    {
        $per_page = 5;
        $config = $this->mockConfigPerPage($per_page);
        $repo = new SentryUserRepository($config);
        foreach (range(1,5) as $key) {
            $input = [
                "email" => "admin@admin.com{$key}",
                "password" => "password",
                "activated" => ($key == 1) ? 1 : 0
            ];
            $repo->create($input);
        }

        $users = $repo->all(["activated" => ""]);
        $this->assertEquals(5, $users->count());
    }
    
    /**
     * @test
     **/
    public function it_allow_ordering_asc_and_desc_by_first_name_last_name_email_last_login_activated_with_all()
    {
        $per_page = 5;
        $config = $this->mockConfigPerPage($per_page);
        $repo = new SentryUserRepository($config);

        $this->createUserWithProfileWithSameValueOnFields($repo, 0);
        $this->createUserWithProfileWithSameValueOnFields($repo, 1);

        $users = $repo->all(["order_by" => "first_name","ordering" => "asc"]);
        $this->assertEquals("0first_name", $users->first()->first_name);
        $users = $repo->all(["ordering" => "desc", "order_by" => "last_name"]);
        $this->assertEquals("1last_name", $users->first()->last_name);
        $users = $repo->all(["order_by" => "email","ordering" => "asc"]);
        $this->assertEquals($users->first()->email, "0@email.com");
        $users = $repo->all(["ordering" => "asc", "order_by" => "last_login"]);
        $this->assertEquals($users->first()->last_login, 0);
        $users = $repo->all(["ordering" => "asc", "order_by" => "activated"]);
        $this->assertEquals($users->first()->activated, 0);

    }

    /**
     * @test
     **/
    public function it_ignore_ordering_by_empty_field_with_all()
    {
        $per_page = 5;
        $config = $this->mockConfigPerPage($per_page);
        $repo = new SentryUserRepository($config);

        $this->createUserWithProfileWithSameValueOnFields($repo, 0);
        $this->createUserWithProfileWithSameValueOnFields($repo, 1);

        $users_ordered = $repo->all(["order_by" => ""]);
        $users = $repo->all();
        $this->assertEquals($users, $users_ordered);
    }

    /**
     * @test
     **/
    public function it_validate_ordering_filter()
    {
        $repo = new SentryUserRepository();

        $invalid_filter = ["order_by" => "invalid "];

        $this->assertFalse($repo->isValidOrderingFilter($invalid_filter));
    }

    /**
     * @test
     * @group ordering
     **/
    public function it_ignore_wrong_column_name_when_ordering_all()
    {
        $per_page = 5;
        $config = $this->mockConfigPerPage($per_page);
        $repo = new SentryUserRepository($config);

        $this->createUserWithProfileWithSameValueOnFields($repo, 0);
        $this->createUserWithProfileWithSameValueOnFields($repo, 1);

        $users_ordered = $repo->all(["order_by" => "wrong_column_name"]);
        $users = $repo->all();
        $this->assertEquals($users, $users_ordered);
        //@todo check why pass and fix
    }

    /**
     * @test
     * @group all
     **/
    public function it_filter_groups_with_all_and_select_distinct_rows()
    {
        // prepare data
        $per_page = 5;
        $config = $this->mockConfigPerPage($per_page);
        list($user_repository, $user) = $this->createUser($config);
        $group1_name = "group 1";
        $group2_name = "group 2";
        $group1 = $this->createGroup($group1_name);
        $group2 = $this->createGroup($group2_name);
        $user_repository->addGroup($user->id, $group1->id);
        $user_repository->addGroup($user->id, $group2->id);

        $users = $user_repository->all(["group_id" => 1]);
        $this->assertEquals(1, $users->count());
        $this->assertEquals("admin@admin.com", $users->first()->email);

        $users = $user_repository->all(["group_id" => 3]);
        $this->assertTrue($users->isEmpty());
    }
    
    /**
     * @test
     **/
    public function it_order_groups_with_all()
    {

        // prepare data
        $config = $this->mockConfigPerPage();
        $user_repository = new SentryUserRepository($config);
        $this->create4Active1InactiveUsers($config);
        $group = $this->createGroup();
        $user_repository->addGroup(2, $group->id);
        $user_repository->addGroup(3, $group->id);

        $users = $user_repository->all(["order_by" => "name", 'ordering' => 'desc']);

        $this->assertEquals(2, $users->first()->id);
    }

    /**
     * @param $config
     * @return SentryUserRepository
     */
    protected function createUserWithProfileForSearch($config)
    {
        $repo         = new SentryUserRepository($config);
        $input        = [
            "email" => "admin@admin.com", "password" => "password", "activated" => 1];
        $user         = $repo->create($input);
        $repo_profile = App::make('profile_repository');
        $input        = [
            "first_name" => "name", "last_name" => "surname", "zip" => "22222", "code" => "12345", "user_id" => $user->id];
        $repo_profile->create($input);

        return $repo;
    }

    /**
     * @return m\MockInterface|\Yay_MockObject
     */
    protected function mockConfigPerPage($per_page = 5)
    {
        $config   = m::mock('ConfigMock');
        $config->shouldReceive('get')->with('authentication::users_per_page')->andReturn($per_page)->getMock();

        return $config;
    }

    /**
     * @param $config
     * @return SentryUserRepository
     */
    protected function create4Active1InactiveUsers($config)
    {
        $repo = new SentryUserRepository($config);
        foreach (range(1, 5) as $key) {
            $input = [
                "email" => "admin@admin.com{$key}", "password" => "password", "activated" => ($key == 1) ? 1 : 0];
            $repo->create($input);
        }

        return $repo;
    }

    /**
     * @param $repo
     */
    protected function createUserWithProfileWithSameValueOnFields($repo, $value)
    {
        $input_user = [
            "email" => "{$value}@email.com",
            "password" => "{$value}",
            "activated" => $value,
        ];
        $user = $repo->create($input_user);
        // set last login
        $repo->update($user->id,[
                      "last_login" => $value
                      ]);

        $repo_profile = App::make('profile_repository');
        $input_profile = [
            "first_name" => "{$value}first_name",
            "last_name" => "{$value}last_name",
            "zip" => "{$value}zip",
            "code" => "{$value}code",
            "user_id" => $user->id
        ];
        $repo_profile->create($input_profile);
    }

    /**
     * @param $config
     * @return array
     */
    protected function createUser($config)
    {
        $user_repository = new SentryUserRepository($config);
        $input_user      = [
            "email" => "admin@admin.com",
            "password" => "password",
            "activated" => 1
        ];
        $user            = $user_repository->create($input_user);

        return [$user_repository, $user];
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