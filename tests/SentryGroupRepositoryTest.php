<?php  namespace Jacopo\Authentication\Tests;

/**
 * Test GroupRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Models\Group;
use Jacopo\Authentication\Repository\SentryGroupRepository;
use Mockery as m;

class SentryGroupRepositoryTest extends DbTestCase {

    public function setUp()
    {
        parent::setUp();

        $this->group_repository = new SentryGroupRepository;
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_creates_a_group()
    {
        $group = $this->group_repository->create(array(
                                          'name'        => 'Users',
                                          'permissions' => array(
                                              'admin' => 1,
                                              'users' => 1,
                                          ),
                                     ));

        $this->assertEquals("Users", $group->name);
    }

    /**
     * @test
     **/
    public function it_find_all_groups_filtered_by_description()
    {
        $repo = $this->createRepoMock();
        $group_created_1 = $repo->create(array(
                                       'name'        => 'Users1',
                                  ));
        $group_created_2 = $repo->create(array(
                                       'name'        => 'Users2',
                                  ));
        $groups = $repo->all(["name" => "1"]);

        $this->assertEquals(1, $groups->count());
        $this->assertEquals("Users1", $groups->first()->name);
    }

    /**
     * @test
     **/
    public function it_find_all_groups()
    {
        $repo = $this->createRepoMock();
        $group_created_1 = $repo->create(array(
                                            'name' => 'Users1',
                                           ));
        $group_created_2 = $repo->create(array(
                                            'name' => 'Users2',
                                           ));
        $groups = $repo->all(["name" => ""]);
        $this->assertEquals(2, $groups->count());

        $groups = $repo->all();
        $this->assertEquals(2, $groups->count());
    }

    /**
     * @test
     **/
    public function it_find_group()
    {

        $group = $this->group_repository->create(array(
                                       'name'        => 'Users',
                                       'permissions' => array(
                                           'admin' => 1,
                                           'users' => 1,
                                       ),
                                        "protected" => 0,
                                  ));
        $group_find = $this->group_repository->find($group->id);

        $this->assertEquals($group_find->toArray(), $group->toArray());
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\UserNotFoundException
     **/
    public function it_find_throws_exception()
    {
        $group_find = $this->group_repository->find(20);

    }

    /**
     * @test
     **/
    public function it_return_all_models()
    {
        $group = $this->group_repository->create(array(
                                       'name'        => 'Users',
                                       'permissions' => array(
                                           'admin' => 1,
                                           'users' => 1,
                                       ),
                                  ));

        $all = $this->group_repository->all();
        $this->assertEquals(1, count($all));
    }

    /**
     * @test
     **/
    public function it_delete_a_group()
    {
        $group = $this->group_repository->create(array(
                                       'name'        => 'Users',
                                       'permissions' => array(
                                           'admin' => 1,
                                           'users' => 1,
                                       ),
                                       'protected'  => 0,
                                  ));

        $success = $this->group_repository->delete($group->id);
        $this->assertTrue($success);
    }


    /**
     * @test
     **/
    public function it_update_a_group()
    {
        $group = $this->group_repository->create(array(
                                       'name'        => 'Users',
                                       'permissions' => array(
                                           'admin' => 1,
                                           'users' => 1,
                                       ),
                                       "protected"  => 0,
                                  ));
        $newname = ["name" => "new name"];
        $this->group_repository->update($group->id, $newname);

        $group_find = $this->group_repository->find($group->id);
        $this->assertEquals($newname["name"], $group_find->name);
    }

    /**
     * @return SentryGroupRepository
     */
    public function createRepoMock()
    {
        $per_page = 5;
        $config   = m::mock('StdClass');
        $config->shouldReceive('get')->with('authentication::groups_per_page')->andReturn($per_page)->getMock();
        $repo = new SentryGroupRepository($config);

        return $repo;
    }

}
 