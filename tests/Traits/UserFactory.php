<?php  namespace Jacopo\Authentication\Tests\Traits;

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

    protected function initializeUserHasher()
    {
        User::setHasher(App::make('sentry.hasher'));
    }

} 