<?php  namespace Jacopo\Authentication\Tests;

/**
 * Test GroupRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Repository\SentryGroupRepository;

class SentryGroupRepositoryTest extends DbTestCase {

    public function setUp()
    {
        parent::setUp();

        $this->r = new SentryGroupRepository;
    }

    /**
     * @test
     **/
    public function it_creates_a_group()
    {
        $group = $this->r->create(array(
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
    public function it_find_group()
    {
        $group = $this->r->create(array(
                                       'name'        => 'Users',
                                       'permissions' => array(
                                           'admin' => 1,
                                           'users' => 1,
                                       ),
                                        "editable" => 1,
                                  ));
        $group_find = $this->r->find($group->id);

        $this->assertEquals($group_find->toArray(), $group->toArray());
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\UserNotFoundException
     **/
    public function it_find_throws_exception()
    {
        $group_find = $this->r->find(20);

    }

    /**
     * @test
     **/
    public function it_return_all_models()
    {
        $group = $this->r->create(array(
                                       'name'        => 'Users',
                                       'permissions' => array(
                                           'admin' => 1,
                                           'users' => 1,
                                       ),
                                  ));

        $all = $this->r->all();
        $this->assertEquals(1, count($all));
    }

    /**
     * @test
     **/
    public function it_delete_a_group()
    {
        $group = $this->r->create(array(
                                       'name'        => 'Users',
                                       'permissions' => array(
                                           'admin' => 1,
                                           'users' => 1,
                                       ),
                                       'editable'  => 1,
                                  ));

        $success = $this->r->delete($group->id);
        $this->assertTrue($success);
    }


    /**
     * @test
     **/
    public function it_update_a_group()
    {
        $group = $this->r->create(array(
                                       'name'        => 'Users',
                                       'permissions' => array(
                                           'admin' => 1,
                                           'users' => 1,
                                       ),
                                       "editable"  => 1,
                                  ));
        $newname = ["name" => "new name"];
        $this->r->update($group->id, $newname);

        $group_find = $this->r->find($group->id);
        $this->assertEquals($newname["name"], $group_find->name);
    }

}
 