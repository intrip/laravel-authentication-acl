<?php
use Illuminate\Session\TokenMismatchException;

/*
  |--------------------------------------------------------------------------
  | Public side (no auth required)
  |--------------------------------------------------------------------------
  |
  */

/**
 * User login and logout
 */
Route::get('/admin/login', [
        "as" => "user.admin.login",
        "uses" => "AuthController@getAdminLogin"
]);
Route::get('/login', [
        "as" => "user.login",
        "uses" =>"AuthController@getClientLogin"
]);
Route::get('/user/logout', [
        "as" => "user.logout",
        "uses" => "AuthController@getLogout"
]);
Route::post('/user/login', [
        "before" => "csrf",
        "uses"   => "AuthController@postAdminLogin",
        "as"     => "user.login.process"
]);
Route::post('/login', [
        "before" => "csrf",
        "uses"   => "AuthController@postClientLogin",
        "as"     => "user.login"
]);

/**
 * Password recovery
 */
Route::get('/user/change-password', [
        "as" => "user.change-password",
        "uses" => 'AuthController@getChangePassword'
]);
Route::get('/user/recovery-password', [
        "as" => "user.recovery-password",
        "uses" => "AuthController@getReminder"
]);
Route::post('/user/change-password/', [
        "before" => "csrf",
        'uses'   => "AuthController@postChangePassword",
        "as"     => "user.reminder.process"
]);

Route::get('/user/change-password-success', [
                "uses" => function ()
                {
                    return View::make('laravel-authentication-acl::client.auth.change-password-success');
                },
                "as"   => "user.change-password-success"
        ]
);
Route::post('/user/reminder', [
        "before" => "csrf",
        'uses'   => "AuthController@postReminder",
        "as"     => "user.reminder"
]);
Route::get('/user/reminder-success', [
        "uses" => function ()
        {
            return View::make('laravel-authentication-acl::client.auth.reminder-success');
        },
        "as"   => "user.reminder-success"
]);


/**
 * User signup
 */
Route::post('/user/signup', [
        "before" => "csrf",
        'uses'   => "UserController@postSignup",
        "as" => "user.signup.process"
]);
Route::get('/user/signup', [
        'uses' => "UserController@signup",
        "as" => "user.signup"
]);
Route::post('captcha-ajax', [
        "before" => "captcha-ajax",
        'uses'   => "UserController@refreshCaptcha",
        "as" => "user.captcha-ajax.process"
]);
Route::get('/user/email-confirmation', [
        'uses' => "UserController@emailConfirmation",
        "as" => "user.email-confirmation"
]);
Route::get('/user/signup-success', [
        "uses" => 'UserController@signupSuccess',
        "as" => "user.signup-success"
]);

/*
  |--------------------------------------------------------------------------
  | Admin side (auth required)
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
            'uses' => 'DashboardController@base'
    ]);

    /**
     * user
     */
    Route::get('/admin/users/list', [
            'as'   => 'users.list',
            'uses' => 'UserController@getList'
    ]);
    Route::get('/admin/users/edit', [
            'as'   => 'users.edit',
            'uses' => 'UserController@editUser'
    ]);
    Route::post('/admin/users/edit', [
            "before" => "csrf",
            'as'     => 'users.edit',
            'uses'   => 'UserController@postEditUser'
    ]);
    Route::get('/admin/users/delete', [
            "before" => "csrf",
            'as'     => 'users.delete',
            'uses'   => 'UserController@deleteUser'
    ]);
    Route::post('/admin/users/groups/add', [
            "before" => "csrf",
            'as'     => 'users.groups.add',
            'uses'   => 'UserController@addGroup'
    ]);
    Route::post('/admin/users/groups/delete', [
            "before" => "csrf",
            'as'     => 'users.groups.delete',
            'uses'   => 'UserController@deleteGroup'
    ]);
    Route::post('/admin/users/editpermission', [
            "before" => "csrf",
            'as'     => 'users.edit.permission',
            'uses'   => 'UserController@editPermission'
    ]);
    Route::get('/admin/users/profile/edit', [
            'as'   => 'users.profile.edit',
            'uses' => 'UserController@editProfile'
    ]);
    Route::post('/admin/users/profile/edit', [
            'before' => 'csrf',
            'as'     => 'users.profile.edit',
            'uses'   => 'UserController@postEditProfile'
    ]);
    Route::post('/admin/users/profile/addField', [
            'before' => 'csrf',
            'as'     => 'users.profile.addfield',
            'uses'   => 'UserController@addCustomFieldType'
    ]);
    Route::post('/admin/users/profile/deleteField', [
            'before' => 'csrf',
            'as'     => 'users.profile.deletefield',
            'uses'   => 'UserController@deleteCustomFieldType'
    ]);
    Route::post('/admin/users/profile/avatar', [
            'before' => 'csrf',
            'as'     => 'users.profile.changeavatar',
            'uses'   => 'UserController@changeAvatar'
    ]);
    Route::get('/admin/users/profile/self', [
            'as'   => 'users.selfprofile.edit',
            'uses' => 'UserController@editOwnProfile'
    ]);

    /**
     * groups
     */
    Route::get('/admin/groups/list', [
            'as'   => 'groups.list',
            'uses' => 'GroupController@getList'
    ]);
    Route::get('/admin/groups/edit', [
            'as'   => 'groups.edit',
            'uses' => 'GroupController@editGroup'
    ]);
    Route::post('/admin/groups/edit', [
            "before" => "csrf",
            'as'     => 'groups.edit',
            'uses'   => 'GroupController@postEditGroup'
    ]);
    Route::get('/admin/groups/delete', [
            "before" => "csrf",
            'as'     => 'groups.delete',
            'uses'   => 'GroupController@deleteGroup'
    ]);
    Route::post('/admin/groups/editpermission', [
            "before" => "csrf",
            'as'     => 'groups.edit.permission',
            'uses'   => 'GroupController@editPermission'
    ]);

    /**
     * permissions
     */
    Route::get('/admin/permissions/list', [
            'as'   => 'permission.list',
            'uses' => 'PermissionController@getList'
    ]);
    Route::get('/admin/permissions/edit', [
            'as'   => 'permission.edit',
            'uses' => 'PermissionController@editPermission'
    ]);
    Route::post('/admin/permissions/edit', [
            "before" => "csrf",
            'as'     => 'permission.edit',
            'uses'   => 'PermissionController@postEditPermission'
    ]);
    Route::get('/admin/permissions/delete', [
            "before" => "csrf",
            'as'     => 'permission.delete',
            'uses'   => 'PermissionController@deletePermission'
    ]);
});

//////////////////// Other routes //////////////////////////

if(Config::get('acl_base.handle_errors'))
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