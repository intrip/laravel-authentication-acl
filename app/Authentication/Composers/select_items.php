<?php
use Jacopo\Authentication\Helpers\FormHelper;
/**
 * permission select
 */
View::composer(['laravel-authentication-acl::admin.user.edit', 'laravel-authentication-acl::admin.group.edit'], function ($view)
{
    $fh = new FormHelper();
    $values_permission = $fh->getSelectValuesPermission();
    $view->with('permission_values', $values_permission);
});
/**
 * group select
 */
View::composer(['laravel-authentication-acl::admin.user.edit', 'laravel-authentication-acl::admin.group.edit',
                'laravel-authentication-acl::admin.user.search'], function ($view)
{
    $fh = new FormHelper();
    $values_group = $fh->getSelectValuesGroups();
    $view->with('group_values', $values_group);
});