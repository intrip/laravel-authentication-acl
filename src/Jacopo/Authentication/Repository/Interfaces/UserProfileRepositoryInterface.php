<?php  namespace Jacopo\Authentication\Repository\Interfaces;
/**
 * Interface UserProfileRepositoryInterface
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
interface UserProfileRepositoryInterface
{
    /**
     * Obtains the profile from the user_id
     * @param $user_id
     * @return mixed
     */
    public function getFromUserId($user_id);
}