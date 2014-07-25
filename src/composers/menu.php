<?php
use Jacopo\Authentication\Classes\Menu\SentryMenuFactory;

/**
 * menu items available depending on permissions
 */
View::composer('laravel-authentication-acl::admin.layouts.*', function ($view)
{
    $menu_items = SentryMenuFactory::create()->getItemListAvailable();
    $view->with('menu_items', $menu_items);
});

/**
 * Dashboard sidebar
 */
View::composer(['laravel-authentication-acl::admin.dashboard.*'], function ($view)
{
    $view->with('sidebar_items', [
            "Dashboard" => [
                    "url"  => URL::route('dashboard.default'),
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
               ], function ($view)
{
    $view->with('sidebar_items', [
            "Users list" => [
                    "url"  => URL::route('users.list'),
                    "icon" => '<i class="fa fa-user"></i>'
            ],
            "Add user"   => [
                    'url'  => URL::route('users.edit'),
                    "icon" => '<i class="fa fa-plus-circle"></i>'
            ]
    ]);
});
/**
 *  Group sidebar
 */
View::composer(['laravel-authentication-acl::admin.group.*'], function ($view)
{
    $view->with('sidebar_items', [
            "Groups list" => [
                    'url'  => URL::route('groups.list'),
                    "icon" => '<i class="fa fa-users"></i>'
            ],
            "Add group"   => [
                    'url'  => URL::route('groups.edit'),
                    "icon" => '<i class="fa fa-plus-circle"></i>'
            ]
    ]);
});
/**
 *  Permission sidebar
 */
View::composer(['laravel-authentication-acl::admin.permission.*'], function ($view)
{
    $view->with('sidebar_items', [
            "Permissions list" => [
                    'url'  => URL::route('permission.list'),
                    "icon" => '<i class="fa fa-lock"></i>'
            ],
            "Add permission"   => [
                    'url'  => URL::route('permission.edit'),
                    "icon" => '<i class="fa fa-plus-circle"></i>'
            ]
    ]);
});