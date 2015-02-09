<?php  namespace Jacopo\Authentication\Classes\Statistics; 
/**
 * Class UserStatistics
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use App;
class UserStatistics
{
    protected $user_repository;

    public function __construct()
    {
        $this->user_repository = App::make('user_repository');
    }

    public function getRegisteredUserNumber()
    {
        return $this->user_repository->all()->count();
    }

    public function getActiveUserNumber()
    {
        return $this->user_repository->all(["activated" => 1])->count();
    }

    public function getPendingUserNumber()
    {
        return $this->user_repository->all(["activated" => 0])->count();
    }

    public function getBannedUserNumber()
    {
        return $this->user_repository->all(["banned" => 1])->count();
    }
}