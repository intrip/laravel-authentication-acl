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
        $config = m::mock('ConfigMock');
        $config->shouldReceive('get')
            ->once()
            ->with('authentication::users_per_page')
            ->andReturn(5)
            ->getMock();
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
        $config = m::mock('ConfigMock');
        $config->shouldReceive('get')
            ->with('authentication::users_per_page')
            ->andReturn(5)
            ->getMock();
        $repo = new SentryUserRepository($config);
        foreach (range(1,5) as $key) {
            $input = [
                "email" => "admin@admin.com{$key}",
                "password" => "password",
                "activated" => ($key == 1) ? 1 : 0
            ];
            $repo->create($input);
        }

        $users = $repo->all(["activated" => 1]);
        $this->assertEquals(1, $users->count());

        $users = $repo->all(["activated" => 0]);
        $this->assertEquals(4, $users->count());
    }

    /**
     * @test
     * @group 1
     **/
    public function it_gets_all_user_filtered_by_first_name_last_name_zip_email_code()
    {
        $per_page = 5;
        $config = m::mock('ConfigMock');
        $config->shouldReceive('get')
            ->with('authentication::users_per_page')
            ->andReturn(5)
            ->getMock();
        $repo = new SentryUserRepository($config);
        $input = [
            "email" => "admin@admin.com",
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
            "user_id" => $user->id
        ];
        $repo_profile->create($input);

        $users = $repo->all(["first_name" => "name"]);
        $this->assertEquals("name", $users->first()->first_name);


    }
}