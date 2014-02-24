<?php namespace Jacopo\Authentication\Repository\Interfaces;
/**
 * Interface UserRepositoryInterface
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
interface UserRepositoryInterface 
{

    /**
     * Activates a user
     * @param integer id
     * @return mixed
     */
    public function activate($id);

    /**
     * Deactivate a user
     * @param $id
     * @return mixed
     */
    public function deactivate($id);

    /**
     * Suspends a user
     * @param $id
     * @param $duration in minutes
     * @return mixed
     */
    public function suspend($id, $duration);

    /**
     * @param $group_id
     * @param $user_id
     * @return mixed
     */
    public function addGroup($user_id, $group_id);

    /**
     * @param $group_id
     * @param $user_id
     * @return mixed
     */
    public function removeGroup($user_id, $group_id);

}