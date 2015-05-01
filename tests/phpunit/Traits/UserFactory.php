<?php  namespace LaravelAcl\Authentication\Tests\Unit\Traits;

use Illuminate\Support\Facades\App;
use LaravelAcl\Authentication\Models\User;

trait UserFactory {

    protected $current_user;

    protected function getUserStub()
    {
        return [
                "email" => $this->faker->email(),
                "password" => $this->faker->text(10),
                "activated" => 1
        ];
    }

    protected function getAdminStub()
    {
        return array_merge($this->getUserStub(), ['permissions' => ['_superadmin' => 1]]);
    }

    protected function getFakeUser()
    {
        $this->current_user = new User($this->getUserStub());
        return $this->current_user;
    }

    protected function getFakeAdmin()
    {
        $this->current_user = new User($this->getAdminStub());
        return $this->current_user;
    }

    protected function getUserProfileStub(User $user)
    {
        return [
                        'user_id'    => $user->id,
                        'vat'        => $this->faker->text('20'),
                        'first_name' => $this->faker->firstName(),
                        'last_name'  => $this->faker->lastName(),
                        'phone'      => $this->faker->phoneNumber(),
                        'state'      => $this->faker->text(20),
                        'city'       => $this->faker->citySuffix(),
                        'country'    => $this->faker->country(),
                        'zip'        => $this->faker->numberBetween(10000, 99999),
                        'address'    => $this->faker->streetAddress()
        ];
    }

    protected function getModelStub()
    {
        return $this->getUserStub();
    }

    protected function initializeUserHasher()
    {
        User::setHasher(App::make('sentry.hasher'));
        return $this;
    }

} 