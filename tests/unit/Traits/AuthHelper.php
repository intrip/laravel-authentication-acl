<?php  namespace LaravelAcl\Authentication\Tests\Unit\Traits;

use Illuminate\Support\Facades\App;

trait AuthHelper {

    use UserFactory;

    public function loginAnUser()
    {
        $this->current_user = $this->getFakeUser();
        $this->loginUser($this->current_user);
    }

    public function loginAnAdmin()
    {
        $this->current_user = $this->getFakeAdmin();
        $this->loginUser($this->current_user);
    }

    public function loginUser($user) {
        App::make('sentry')->login($user);
    }

} 
