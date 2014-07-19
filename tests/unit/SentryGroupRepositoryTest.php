<?php  namespace Jacopo\Authentication\Tests\Unit;

/**
 * Test GroupRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Repository\SentryGroupRepository;
use Mockery as m;

class SentryGroupRepositoryTest extends DbTestCase
{

    protected $group_class;
    protected $group_repository;

    public function setUp()
    {
        parent::setUp();
        $this->group_repository = new SentryGroupRepository;
        $this->group_class = 'Jacopo\Authentication\Models\Group';
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function canCreateGroup()
    {
        $group = $this->group_repository->create(['name'        => 'Users',
                                                  'permissions' => ['admin' => 1, 'users' => 1,],
                                                 ]);

        $this->assertEquals("Users", $group->name);
    }

    /**
     * @test
     **/
    public function canFindGroupsFilteredByDescription()
    {
        $repo = $this->mockConfigPerPage();
        $group_name = "Users1";
        $this->make($this->group_class, ["name" => $group_name]);

        $groups = $repo->all(["name" => "1"]);

        $this->assertEquals(1, $groups->count());
        $this->assertEquals($group_name, $groups->first()->name);
    }

    /**
     * @test
     **/
    public function canFindAllGroups()
    {
        $repo = $this->mockConfigPerPage();
        $this->times(2)->make($this->group_class);

        $groups = $repo->all(["name" => ""]);
        $this->assertEquals(2, $groups->count());

        $groups = $repo->all();
        $this->assertEquals(2, $groups->count());
    }

    /**
     * @test
     **/
    public function canFindAGroup()
    {
        $group = $this->make($this->group_class);

        $group_found = $this->group_repository->find($group[0]->id);

        $this->assertObjectHasAllAttributes($group_found->toArray(), $group[0],['permissions','protected']);
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\UserNotFoundException
     **/
    public function itHandleErrorsWithFind()
    {
        $this->group_repository->find(20);
    }

    /**
     * @test
     **/
    public function itCanGetAllGroupsUnfiltered()
    {
        $this->make($this->group_class);

        $all = $this->group_repository->all();
        $this->assertEquals(1, count($all));
    }

    /**
     * @test
     **/
    public function it_delete_a_group()
    {
        $groups = $this->make($this->group_class);

        $success = $this->group_repository->delete($groups[0]->id);
        $this->assertTrue($success);
    }


    /**
     * @test
     **/
    public function it_update_a_group()
    {
        $groups = $this->make($this->group_class);
        $newname = ["name" => "new name"];
        $this->group_repository->update($groups[0]->id, $newname);

        $group_find = $this->group_repository->find($groups[0]->id);
        $this->assertEquals($newname["name"], $group_find->name);
    }

    /**
     * @return SentryGroupRepository
     */
    public function mockConfigPerPage()
    {
        $per_page = 5;
        $config = m::mock('StdClass');
        $config->shouldReceive('get')->with('laravel-authentication-acl::groups_per_page')->andReturn($per_page)->getMock();
        $repo = new SentryGroupRepository($config);

        return $repo;
    }

    protected function getModelStub()
    {
        return [
                "name" => $this->faker->unique()->text(10)
        ];
    }
}
 