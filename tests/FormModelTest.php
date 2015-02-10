<?php namespace Jacopo\Library\Tests;

use Jacopo\Library\Form\FormModel;
use Mockery as m;
use Jacopo\Library\Tests\TestCase;

class FormModelTest extends TestCase
{
    protected $faker;
    protected $repo;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @expectedException \Jacopo\Library\Exceptions\ValidationException
     */
    public function testProcessThrowsValidationException()
    {
        $stub_validator = new ValidatorInterfaceStubFalse();

        $form = new FormModel($stub_validator, new \StdClass());
        $form->process(array());
    }

    public function testProcessCreateWorks()
    {
        $stub_validator = new ValidatorInterfaceStub();
        $obj = new \StdClass();
        $obj->id="1";
        $mock_repo = m::mock('StdClass')->shouldReceive(array(
                                                             'create' => $obj,
                                                        ))
            ->getMock();
        $form = new FormModel($stub_validator, $mock_repo);
        $form->process(array());
    }

    public function testProcessUpdateWorks()
    {
        $stub_validator = new ValidatorInterfaceStub();
        $obj = new \StdClass();
        $obj->id="1";
        $mock_repo = m::mock('StdClass')->shouldReceive(array(
                                                             "update" => $obj,
                                                        ))
            ->getMock();
        $form = new FormModel($stub_validator, $mock_repo);
        $form->process(array("id" => "1"));
    }

    /**
     * @expectedException \Jacopo\Library\Exceptions\NotFoundException
     */
    public function testProcessThrowNotFound()
    {
        $stub_validator = new ValidatorInterfaceStub();

        $mock_repo = m::mock('StdClass')->shouldReceive('update')->andThrow(new \Jacopo\Library\Exceptions\NotFoundException)->getMock();
        $form = new FormModel($stub_validator, $mock_repo);
        $form->process(array("id" => "1"));
    }
    
    /** @test */
    public function it_deletes_a_model()
    {
        $stub_validator = new ValidatorInterfaceStub();
        $mock_repo = m::mock('StdClass')->shouldReceive("delete")->getMock();
        $form = new FormModel($stub_validator, $mock_repo);
        $form->delete(array("id" => "1"));
    }

    /**
     * @test
     * @expectedException \Jacopo\Library\Exceptions\NotFoundException
     *
     */
    public function it_throws_NotFoundException()
    {
        $stub_validator = new ValidatorInterfaceStub();
        $mock_repo = m::mock('StdClass')->shouldReceive("delete")->andThrow(new
        \Illuminate\Database\Eloquent\ModelNotFoundException)->getMock();
        $form = new FormModel($stub_validator, $mock_repo);
        $form->delete(array("id" => "1"));
    }
}

class ValidatorInterfaceStub implements \Jacopo\Library\Validators\ValidatorInterface
{
    public function validate($input){
        return true;
    }
    public function getErrors(){}

}

class ValidatorInterfaceStubFalse extends ValidatorInterfaceStub
{
    public function validate($input){
        return false;
    }
}