<?php  namespace Jacopo\Authentication\Tests\Unit\Validators;

use Illuminate\Support\Facades\Event;
use Jacopo\Authentication\Tests\Unit\DbTestCase;
use Jacopo\Authentication\Tests\Unit\Traits\UserFactory;
use Jacopo\Authentication\Validators\UserValidator;

class UserValidatorTest extends DbTestCase  {
    use UserFactory;

    protected $validator;

    public function setUp()
    {
        parent::setUp();
        $this->validator = new UserValidator();
        $this->validator->resetStatic();
        $this->initializeUserHasher();
    }

    /**
     * @test
     **/
    public function canValidateUniqueEmailOnUpdate()
    {
        $user_created = $this->make('Jacopo\Authentication\Models\User', $this->getUserStub());

        $input = [
            "id" => 1,
            "form_name" => "user",
            "email" => $user_created[0]->email
        ];

        $this->assertTrue($this->validator->validate($input));
    }

    /**
     * @test
     **/
    public function canValidateUniqueEmailOnCreate()
    {
        $user_created = $this->make('Jacopo\Authentication\Models\User', $this->getUserStub());

        $input = [
                "form_name" => "user",
                "email", $user_created[0]->email,
                "password" => 'password',
                "password_confirmation" => 'password',
        ];

        $this->assertFalse($this->validator->validate($input));

        $input = $this->setUnusedEmail($input);
        $this->assertTrue($this->validator->validate($input));
    }

    /**
     * @param $input
     * @return mixed
     */
    protected function setUnusedEmail($input)
    {
        $input["email"] = "other@email.com";
        return $input;
    }
}
 