<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin panel menu items
    |--------------------------------------------------------------------------
    |
    | Here you can edit the items to show in the admin menu(on top of the page)
    |
    */
    "list" => [
                [
                    /*
                     * the name of the link: you will see it in the menu
                     */
                    "name" => "Users",
                    /* the route name associated to the link: used to set
                     * the 'active' flag
                     */
                    "route" => "users",
                    /*
                     * the acual link associated to the menu item
                     */
                    "link" => URL::route('users.dashboard'),
                    /*
                     * the list of 'permission name' associated to the menu
                     * item: if the logged use has one or more of the permission
                     * in the list he can see the menu link and access the area
                     * associated with that.
                     * Every route that you create with the 'route' as a prefix
                     * will check for the permissions and throw a 401 error if the
                     * check fails (for example in this case every route named users.*)
                     */
                    "permissions" => ["_superadmin", "_user-editor"]
                ],
                [
                    "name" => "Groups",
                    "route" => "groups",
                    "link" => URL::route('groups.list'),
                    "permissions" => ["_superadmin", "_group-editor"]
                ],
                [
                    "name" => "Permission",
                    "route" => "permission",
                    "link" => URL::route('permission.list'),
                    "permissions" => ["_superadmin", "_permission-editor"]
                ],
      ]
];