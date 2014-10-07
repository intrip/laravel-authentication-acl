<?php  namespace Jacopo\Authentication\Tests\Unit\Traits;

use Illuminate\Support\Facades\App;
use Jacopo\Authentication\Models\User;

trait UserFactory {

    protected function getUserStub()
    {
        return [
                "email" => $this->faker->email(),
                "password" => $this->faker->text(10),
                "activated" => 1,
        ];
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
        return [];
    }

    protected function initializeUserHasher()
    {
        User::setHasher(App::make('sentry.hasher'));
        return $this;
    }

} 