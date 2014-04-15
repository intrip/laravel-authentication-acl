<?php

/**
 * the site name
 */
View::composer('authentication::*', function ($view){
    $view->with('app_name', Config::get('authentication::app_name') );
});

/**
 * the logged user
 */
View::composer('authentication::*', function ($view){
    $view->with('logged_user', App::make('authenticator')->getLoggedUser() );
});

use Jacopo\Authentication\Classes\Menu\SentryMenuFactory;
/**
 * menu items available depending on permissions
 */
View::composer('authentication::admin.layouts.*', function ($view){
    $menu_items = SentryMenuFactory::create()->getItemListAvailable();
    $view->with('menu_items', $menu_items);
});

/**
 * User sidebar
 */
View::composer(['authentication::admin.user.*'], function ($view){
    $view->with('sidebar_items', [
//                                     "Dashboard" => [
//                                         "url" => '#',
//                                         "icon" => '<i class="fa fa-tachometer"></i>'
//                                     ],
                                    "Users list" => [
                                        "url" => URL::route('users.list'),
                                        "icon" => '<i class="fa fa-user"></i>'
                                    ],
                                    "Add user" => [
                                        'url' => URL::route('users.edit'),
                                        "icon" => '<i class="fa fa-plus-circle"></i>'
                                    ]
                                 ]);
});
/**
 *  Group sidebar
 */
View::composer(['authentication::admin.group.*'], function ($view){
        $view->with('sidebar_items', [
            "Groups list" => [
            'url' => URL::route('groups.list'),
            "icon" => '<i class="fa fa-users"></i>'
        ],
            "Add group" => [
            'url' => URL::route('groups.edit'),
            "icon" => '<i class="fa fa-plus-circle"></i>'
        ]
        ]);
});
/**
 *  Permission sidebar
 */
View::composer(['authentication::admin.permission.*'], function ($view){
    $view->with('sidebar_items', [
                                 "Permissions list" => [
                                     'url' => URL::route('permission.list'),
                                     "icon" => '<i class="fa fa-lock"></i>'
                                 ],
                                 "Add permission" => [
                                     'url' => URL::route('permission.edit'),
                                     "icon" => '<i class="fa fa-plus-circle"></i>'
                                 ]
                                 ]);
});



use Jacopo\Authentication\Helpers\FormHelper;
/**
 * permission select
 */
View::composer(['authentication::admin.user.edit','authentication::admin.group.edit'], function ($view){
    $fh = new FormHelper();
    $values_permission = $fh->getSelectValuesPermission();
    $view->with('permission_values', $values_permission);
});
/**
 * group select
 */
View::composer(['authentication::admin.user.edit','authentication::admin.group.edit', 'authentication::admin.user.search'], function ($view){
    $fh = new FormHelper();
    $values_group = $fh->getSelectValuesGroups();
    $view->with('group_values', $values_group);
});