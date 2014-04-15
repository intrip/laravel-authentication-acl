<?php  namespace Jacopo\Authentication\Interfaces;
/**
 * Interface MenuCollectionInterface
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
interface MenuCollectionInterface
{
    /**
     * Obtain all the menu items
     * @return \Jacopo\Authentication\Classes\MenuItem
     */
    public function getItemList();

    /**
     * Obtain the menu items that the current user can access
     * @return mixed
     */
    public function getItemListAvailable();

} 