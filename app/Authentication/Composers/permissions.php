<?php
/**
 * Permission to add custom profile field
 */
View::composer(['laravel-authentication-acl::admin.user.profile','laravel-authentication-acl::admin.user.self-profile'], function($view){
    $auth_helper = App::make('authentication_helper');
    $can_add_fields = $auth_helper->checkCustomProfileEditPermission() ? true : false;

    $view->with(['can_add_fields' => $can_add_fields]);
});