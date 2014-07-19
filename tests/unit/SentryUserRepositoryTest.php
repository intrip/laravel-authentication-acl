<?php  namespace Jacopo\Authentication\Tests\Unit;

use App, Config;
use Jacopo\Authentication\Repository\SentryUserRepository;
use Mockery as m;
use Cartalyst\Sentry\Users\UserExistsException;

/**
 * Test SentryUserRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class SentryUserRepositoryTest extends DbTestCase
{

    protected $user_repository;

    public function setUp()
    {
        parent::setUp();

        $this->user_repository = new SentryUserRepository;
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function canCreateAnUser()
    {
        $user_data = [
                "email"     => "user@mail.com",
                "password"  => "testpassword",
                "activated" => 1,
                "banned"    => 1
        ];

        $this->user_repository->create($user_data);

        $saved_user = $this->user_repository->findByLogin($user_data["email"]);

        $this->objectHasAllArrayAttributes($user_data, $saved_user, ["password"]);
    }

    /**
     * @test
     **/
    public function it_find_user_from_a_group()
    {
        $this->createUser([]);
        $group_repo = App::make('group_repository');
        $input = [
                "name" => "admin"
        ];
        $group_repo->create($input);

        $this->user_repository->addGroup(1, 1);
        $users = $this->user_repository->findFromGroupName('admin');
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
        $this->user_repository = new SentryUserRepository();
        $this->user_repository->create([
                                               "email"     => "email",
                                               "password"  => "password",
                                               "activated" => "activated",
                                       ]);
    }

    /**
     * @test
     **/
    public function it_activate_a_user()
    {
        $input = [
                "email"           => "admin@admin.com",
                "password"        => "password",
                "activated"       => 0,
                "activation_code" => "code"
        ];
        $this->user_repository->create($input);

        $this->user_repository->activate("admin@admin.com");

        $user = $this->user_repository->find(1);
        $this->assertTrue($user->activated);
        $this->assertNotEmpty($user->activated_at);
        $this->assertNull($user->activation_code);
    }

    /**
     * @test
     **/
    public function it_search_input_with_all()
    {
        $mock_config = $this->setConfigPerPage();
        $user_repository = new SentryUserRepository($mock_config);
        $input = [];
        $mock_search_repository = m::mock('StdClass')
                                   ->shouldReceive('all')
                                   ->once()
                                   ->with($input)
                                   ->getMock();

        $user_repository->all($input, $mock_search_repository);
    }

    /**
     * @test
     **/
    public function it_find_user_by_login_name()
    {
        $this->createUser([]);

        $user = $this->user_repository->findByLogin("admin@admin.com");
        $this->assertEquals("admin@admin.com", $user->email);
    }

    /**
     * @test
     * @expectedException Jacopo\Authentication\Exceptions\UserNotFoundException
     **/
    public function it_throws_exception_if_cannot_find_user_by_login()
    {
        $user = $this->user_repository->findByLogin("admin@admin.com");
        $this->assertEquals("admin@admin.com", $user->email);
    }

    /**
     * @test
     **/
    public function canCallAllOnModel()
    {
        $mock_model_all = m::mock('StdClass')
                           ->shouldReceive('all')
                           ->once()
                           ->getMock();
        $this->user_repository->setModel($mock_model_all);
        $this->user_repository->allModel();
    }

    /**
     * @param $config
     * @return array
     */
    protected function createUser($config)
    {
        $user_repository = new SentryUserRepository($config);
        $input_user = [
                "email"     => "admin@admin.com",
                "password"  => "password",
                "activated" => 1
        ];
        $user = $user_repository->create($input_user);

        return [$user_repository, $user];
    }

    protected function createGroup($name = "group name")
    {
        $group_repository = App::make('group_repository');
        $input_group = [
                "name" => $name,];
        $group = $group_repository->create($input_group);
        return $group;
    }

    /**
     * @return m\MockInterface|\Yay_MockObject
     */
    protected function setConfigPerPage($per_page = 5)
    {
        Config::set('laravel-authentication-acl::users_per_page', $per_page);
    }
}