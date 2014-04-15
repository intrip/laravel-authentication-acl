<?php namespace Jacopo\Authentication\Interfaces;
/**
 * Interface MenuIterface
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
interface MenuInterface
{
    /**
     * Check if the current user have access to the menu item
     * @return boolean
     */
    public function havePermission();

    /**
     * Obtain the menu link
     * @return mixed
     */
    public function getLink();

    /**
     * Obtain the menu name
     * @return mixed
     */
    public function getName();
}