<?php  namespace Jacopo\Authentication\Tests;
use App;
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
        $repo = App::make('user_repository');
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
    public function it_trhows_exception_if_user_aready_exists()
    {
        $mock_sentry = m::mock('StdClass');
        $mock_sentry->shouldReceive('createUser')
            ->andThrow(new UserExistsException)
            ->getMock();
        App::instance('sentry', $mock_sentry);
        $repo = App::make('user_repository');
        $repo->create([
                          "email" => "email",
                          "password" => "password",
                          "activated" => "activated",
                      ]);
    }
}
 