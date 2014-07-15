<?php

/**
 * the site name
 */
View::composer('laravel-authentication-acl::*', function ($view){
    $view->with('app_name', Config::get('laravel-authentication-acl::app_name') );
});

/**
 * the logged user
 */
View::composer('laravel-authentication-acl::*', function ($view){
    $view->with('logged_user', App::make('authenticator')->getLoggedUser() );
});

use Jacopo\Authentication\Classes\Menu\SentryMenuFactory;
/**
 * menu items available depending on permissions
 */
View::composer('laravel-authentication-acl::admin.layouts.*', function ($view){
    $menu_items = SentryMenuFactory::create()->getItemListAvailable();
    $view->with('menu_items', $menu_items);
});

/**
 * Dashboard sidebar
 */
View::composer(['laravel-authentication-acl::admin.dashboard.*'], function ($view){
    $view->with('sidebar_items', [
            "Dashboard" => [
                    "url" => URL::route('dashboard.default'),
                    "icon" => '<i class="fa fa-tachometer"></i>'
            ]
    ]);
});

/**
 * User sidebar
 */
View::composer([
                       'laravel-authentication-acl::admin.user.edit',
                       'laravel-authentication-acl::admin.user.groups',
                       'laravel-authentication-acl::admin.user.list',
                       'laravel-authentication-acl::admin.user.profile',
               ], function ($view){
    $view->with('sidebar_items', [
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
View::composer(['laravel-authentication-acl::admin.group.*'], function ($view){
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
View::composer(['laravel-authentication-acl::admin.permission.*'], function ($view){
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

use Jacopo\Authentication\Classes\Statistics\UserStatistics;
use Jacopo\Authentication\Helpers\FormHelper;
/**
 * permission select
 */
View::composer(['laravel-authentication-acl::admin.user.edit','laravel-authentication-acl::admin.group.edit'], function ($view){
    $fh = new FormHelper();
    $values_permission = $fh->getSelectValuesPermission();
    $view->with('permission_values', $values_permission);
});
/**
 * group select
 */
View::composer(['laravel-authentication-acl::admin.user.edit','laravel-authentication-acl::admin.group.edit', 'laravel-authentication-acl::admin.user.search'], function ($view){
    $fh = new FormHelper();
    $values_group = $fh->getSelectValuesGroups();
    $view->with('group_values', $values_group);
});
/**
 * Dashboard information
 */
View::composer(['laravel-authentication-acl::admin.dashboard.*'], function($view){
    $user_statistics = new UserStatistics();
    $registered = $user_statistics->getRegisteredUserNumber();
    $active = $user_statistics->getActiveUserNumber();
    $pending = $user_statistics->getPendingUserNumber();
    $banned = $user_statistics->getBannedUserNumber();

    $view->with(['registered' => $registered,"active" => $active,"pending" => $pending,"banned" => $banned]);
});
/**
 * Permission to add custom profile field
 */
View::composer(['laravel-authentication-acl::admin.user.profile','laravel-authentication-acl::admin.user.self-profile'], function($view){
    $auth_helper = App::make('authentication_helper');
    $can_add_fields = $auth_helper->checkCustomProfileEditPermission() ? true : false;

    $view->with(['can_add_fields' => $can_add_fields]);
});