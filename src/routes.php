<?php

/*
  |--------------------------------------------------------------------------
  | Public side
  |--------------------------------------------------------------------------
  |
  */
use Illuminate\Session\TokenMismatchException;

/**
 * User login and logout
 */
Route::get('/admin/login', "Jacopo\\Authentication\\Controllers\\AuthController@getAdminLogin");
Route::get('/login', "Jacopo\\Authentication\\Controllers\\AuthController@getClientLogin");
Route::get('/user/logout', "Jacopo\\Authentication\\Controllers\\AuthController@getLogout");
Route::post('/user/login', [
        "before" => "csrf",
        "uses"   => "Jacopo\\Authentication\\Controllers\\AuthController@postAdminLogin"
]);
Route::post('/login', [
        "before" => "csrf",
        "uses"   => "Jacopo\\Authentication\\Controllers\\AuthController@postClientLogin"
]);
/**
 * Password recovery
 */
Route::get('/user/change-password', 'Jacopo\Authentication\Controllers\AuthController@getChangePassword');
Route::get('/user/recover-password', "Jacopo\\Authentication\\Controllers\\AuthController@getReminder");
Route::post('/user/change-password/', [
        "before" => "csrf",
        'uses'   => "Jacopo\\Authentication\\Controllers\\AuthController@postChangePassword"
]);
Route::get('/user/change-password-success', function ()
{
    return View::make('laravel-authentication-acl::client.auth.change-password-success');
});
Route::post('/user/reminder', [
        "before" => "csrf",
        'uses'   => "Jacopo\\Authentication\\Controllers\\AuthController@postReminder"
]);
Route::get('/user/reminder-success', function ()
{
    return View::make('laravel-authentication-acl::client.auth.reminder-success');
});
/**
 * User signup
 */
Route::post('/user/signup', [
        "before" => "csrf",
        'uses'   => "Jacopo\\Authentication\\Controllers\\UserController@postSignup"
]);
Route::get('/user/signup', [
        'uses' => "Jacopo\\Authentication\\Controllers\\UserController@signup"
]);
Route::post('captcha-ajax', [
        "before" => "captcha-ajax",
        'uses'   => "Jacopo\\Authentication\\Controllers\\UserController@refreshCaptcha"
]);
Route::get('/user/email-confirmation', ['uses' => "Jacopo\\Authentication\\Controllers\\UserController@emailConfirmation"]);
Route::get('/user/signup-success', 'Jacopo\Authentication\Controllers\UserController@signupSuccess');

/*
  |--------------------------------------------------------------------------
  | Admin side
  |--------------------------------------------------------------------------
  |
  */
Route::group(['before' => ['admin_logged', 'can_see']], function ()
{
    /**
     * dashboard
     */
    Route::get('/admin/users/dashboard', [
            'as'   => 'dashboard.default',
            'uses' => 'Jacopo\Authentication\Controllers\DashboardController@base'
    ]);

    /**
     * user
     */
    Route::get('/admin/users/list', [
            'as'   => 'users.list',
            'uses' => 'Jacopo\Authentication\Controllers\UserController@getList'
    ]);
    Route::get('/admin/users/edit', [
            'as'   => 'users.edit',
            'uses' => 'Jacopo\Authentication\Controllers\UserController@editUser'
    ]);
    Route::post('/admin/users/edit', [
            "before" => "csrf",
            'as'     => 'users.edit',
            'uses'   => 'Jacopo\Authentication\Controllers\UserController@postEditUser'
    ]);
    Route::get('/admin/users/delete', [
            "before" => "csrf",
            'as'     => 'users.delete',
            'uses'   => 'Jacopo\Authentication\Controllers\UserController@deleteUser'
    ]);
    Route::post('/admin/users/groups/add', [
            "before" => "csrf",
            'as'     => 'users.groups.add',
            'uses'   => 'Jacopo\Authentication\Controllers\UserController@addGroup'
    ]);
    Route::post('/admin/users/groups/delete', [
            "before" => "csrf",
            'as'     => 'users.groups.delete',
            'uses'   => 'Jacopo\Authentication\Controllers\UserController@deleteGroup'
    ]);
    Route::post('/admin/users/editpermission', [
            "before" => "csrf",
            'as'     => 'users.edit.permission',
            'uses'   => 'Jacopo\Authentication\Controllers\UserController@editPermission'
    ]);
    Route::get('/admin/users/profile/edit', [
            'as'   => 'users.profile.edit',
            'uses' => 'Jacopo\Authentication\Controllers\UserController@editProfile'
    ]);
    Route::post('/admin/users/profile/edit', [
            'before' => 'csrf',
            'as'     => 'users.profile.edit',
            'uses'   => 'Jacopo\Authentication\Controllers\UserController@postEditProfile'
    ]);
    Route::post('/admin/users/profile/addField', [
            'before' => 'csrf',
            'as'     => 'users.profile.addfield',
            'uses'   => 'Jacopo\Authentication\Controllers\UserController@addCustomFieldType'
    ]);
    Route::post('/admin/users/profile/deleteField', [
            'before' => 'csrf',
            'as'     => 'users.profile.deletefield',
            'uses'   => 'Jacopo\Authentication\Controllers\UserController@deleteCustomFieldType'
    ]);
    Route::post('/admin/users/profile/avatar', [
            'before' => 'csrf',
            'as'     => 'users.profile.changeavatar',
            'uses'   => 'Jacopo\Authentication\Controllers\UserController@changeAvatar'
    ]);
    Route::get('/admin/users/profile/self', [
        'as' => 'users.selfprofile.edit',
        'uses' => 'Jacopo\Authentication\Controllers\UserController@editOwnProfile'
    ]);

    /**
     * groups
     */
    Route::get('/admin/groups/list', [
            'as'   => 'groups.list',
            'uses' => 'Jacopo\Authentication\Controllers\GroupController@getList'
    ]);
    Route::get('/admin/groups/edit', [
            'as'   => 'groups.edit',
            'uses' => 'Jacopo\Authentication\Controllers\GroupController@editGroup'
    ]);
    Route::post('/admin/groups/edit', [
            "before" => "csrf",
            'as'     => 'groups.edit',
            'uses'   => 'Jacopo\Authentication\Controllers\GroupController@postEditGroup'
    ]);
    Route::get('/admin/groups/delete', [
            "before" => "csrf",
            'as'     => 'groups.delete',
            'uses'   => 'Jacopo\Authentication\Controllers\GroupController@deleteGroup'
    ]);
    Route::post('/admin/groups/editpermission', [
            "before" => "csrf",
            'as'     => 'groups.edit.permission',
            'uses'   => 'Jacopo\Authentication\Controllers\GroupController@editPermission'
    ]);

    /**
     * permissions
     */
    Route::get('/admin/permissions/list', [
            'as'   => 'permission.list',
            'uses' => 'Jacopo\Authentication\Controllers\PermissionController@getList'
    ]);
    Route::get('/admin/permissions/edit', [
            'as'   => 'permission.edit',
            'uses' => 'Jacopo\Authentication\Controllers\PermissionController@editPermission'
    ]);
    Route::post('/admin/permissions/edit', [
            "before" => "csrf",
            'as'     => 'permission.edit',
            'uses'   => 'Jacopo\Authentication\Controllers\PermissionController@postEditPermission'
    ]);
    Route::get('/admin/permissions/delete', [
            "before" => "csrf",
            'as'     => 'permission.delete',
            'uses'   => 'Jacopo\Authentication\Controllers\PermissionController@deletePermission'
    ]);
});

//////////////////// Other routes //////////////////////////

if(Config::get('laravel-authentication-acl::handle_errors'))
{
    App::error(function (RuntimeException $exception, $code)
    {
        switch($code)
        {
            case '404':
                return View::make('laravel-authentication-acl::client.exceptions.404');
                break;
            case '401':
                return View::make('laravel-authentication-acl::client.exceptions.401');
                break;
            case '500':
                return View::make('laravel-authentication-acl::client.exceptions.500');
                break;
        }
    });

    App::error(function (TokenMismatchException $exception)
    {
        return View::make('laravel-authentication-acl::client.exceptions.500');
    });
}