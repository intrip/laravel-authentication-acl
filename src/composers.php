<?php

/**
 * Send to the view the site name
 */
View::composer('authentication::*', function ($view){
    $view->with('app_name', Config::get('authentication::app_name') );
});

use Jacopo\Authentication\Classes\Menu\SentryMenuFactory;
/**
 * Send the menu items
 */
View::composer('authentication::layouts.*', function ($view){
    $menu_items = SentryMenuFactory::create()->getItemListAvailable();
    $view->with('menu_items', $menu_items);
});

/**
 * Create users sidebar
 */
View::composer(['authentication::user.*', 'authentication::group.*', 'authentication::permission.*'], function ($view){
    $view->with('sidebar_items', [
                                     "Dashboard" => [
                                         "url" => '#',
                                         "icon" => '<i class="fa fa-tachometer"></i>'
                                     ],
                                    "Users list" => [
                                        "url" => URL::route('users.list'),
                                        "icon" => '<i class="fa fa-user"></i>'
                                    ],
                                    "Add user" => [
                                        'url' => URL::route('users.edit'),
                                        "icon" => '<i class="fa fa-plus-circle"></i>'
                                    ],
                                    "Groups list" => [
                                        'url' => URL::route('users.groups.list'),
                                        "icon" => '<i class="fa fa-users"></i>'
                                    ],
                                    "Add group" => [
                                        'url' => URL::route('users.groups.edit'),
                                        "icon" => '<i class="fa fa-plus-circle"></i>'
                                    ],
                                    "Permissions list" => [
                                        'url' => URL::route('users.permission.list'),
                                        "icon" => '<i class="fa fa-lock"></i>'
                                    ],
                                    "Add permission" => [
                                        'url' => URL::route('users.permission.edit'),
                                        "icon" => '<i class="fa fa-plus-circle"></i>'
                                    ]
                                 ]);
});

use Jacopo\Authentication\Helpers\FormHelper;
/**
 * Sends the permission select to the view
 */
View::composer(['authentication::user.edit','authentication::group.edit'], function ($view){
    $fh = new FormHelper();
    $values_permission = $fh->getSelectValuesPermission();
    $view->with('permission_values', $values_permission);
});
/**
 * Sends the group select to the view
 */
View::composer(['authentication::user.edit','authentication::group.edit'], function ($view){
    $fh = new FormHelper();
    $values_group = $fh->getSelectValuesGroups();
    $view->with('group_values', $values_group);
});