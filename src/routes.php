<?php
//////////////////// Client views //////////////////////////
/**
 * User login and logout
 */
Route::get('/admin/login', "Jacopo\\Authentication\\Controllers\\AuthController@getAdminLogin");
Route::get('/login', "Jacopo\\Authentication\\Controllers\\AuthController@getClientLogin");
Route::get('/user/logout', "Jacopo\\Authentication\\Controllers\\AuthController@getLogout");
Route::post('/user/login', ["before" => "csrf", "uses" => "Jacopo\\Authentication\\Controllers\\AuthController@postAdminLogin"]);
Route::post('/login', ["before" => "csrf", "uses" => "Jacopo\\Authentication\\Controllers\\AuthController@postClientLogin"]);
/**
 * Password recovery
 */
Route::get('/user/change-password', 'Jacopo\Authentication\Controllers\AuthController@getChangePassword');
Route::get('/user/recover-password', "Jacopo\\Authentication\\Controllers\\AuthController@getReminder");
Route::post('/user/change-password/', ["before" => "csrf", 'uses' => "Jacopo\\Authentication\\Controllers\\AuthController@postChangePassword"]);
Route::post('/user/reminder', ["before" => "csrf", 'uses' => "Jacopo\\Authentication\\Controllers\\AuthController@postReminder"]);
/**
 * User signup
 */
Route::post('/user/signup', ["before" => "csrf", 'uses' => "Jacopo\\Authentication\\Controllers\\UserController@postSignup"]);
Route::get('/user/signup', ['uses' => "Jacopo\\Authentication\\Controllers\\UserController@signup"]);
Route::get('/user/email-confirmation', ['uses' => "Jacopo\\Authentication\\Controllers\\UserController@emailConfirmation"]);
Route::get('/user/signup-success', 'Jacopo\\Authentication\\Controllers\\UserController@signupSuccess');
//////////////////// Admin Panel //////////////////////////

Route::group( ['before' => ['logged', 'can_see']], function()
{
    Route::get('/admin/home', ['as' => 'home', function(){
        return View::make('authentication::home.home');
    }]);

    // user
    Route::get('/admin/users/list', ['as' => 'users.list', 'uses' => 'Jacopo\Authentication\Controllers\UserController@getList']);
    Route::get('/admin/users/edit', ['as' => 'users.edit', 'uses' => 'Jacopo\Authentication\Controllers\UserController@editUser']);
    Route::post('/admin/users/edit', ["before" => "csrf", 'as' => 'users.edit', 'uses' => 'Jacopo\Authentication\Controllers\UserController@postEditUser']);
    Route::get('/admin/users/delete', ["before" => "csrf", 'as' => 'users.delete', 'uses' => 'Jacopo\Authentication\Controllers\UserController@deleteUser']);
    Route::post('/admin/users/groups/add', ["before" => "csrf", 'as' => 'users.groups.add', 'uses' => 'Jacopo\Authentication\Controllers\UserController@addGroup']);
    Route::post('/admin/users/groups/delete', ["before" => "csrf", 'as' => 'users.groups.delete', 'uses' => 'Jacopo\Authentication\Controllers\UserController@deleteGroup']);
    Route::post('/admin/users/editpermission', ["before" => "csrf", 'as' => 'users.edit.permission', 'uses' => 'Jacopo\Authentication\Controllers\UserController@editPermission']);
    Route::get('/admin/users/profile/edit', ['as' => 'users.profile.edit', 'uses' => 'Jacopo\Authentication\Controllers\UserController@editProfile']);
    Route::post('/admin/users/profile/edit', ['before' => 'csrf', 'as' => 'users.profile.edit', 'uses' => 'Jacopo\Authentication\Controllers\UserController@postEditProfile']);

    // groups
    Route::get('/admin/groups/list', ['as' => 'groups.list', 'uses' => 'Jacopo\Authentication\Controllers\GroupController@getList']);
    Route::get('/admin/groups/edit', ['as' => 'groups.edit', 'uses' => 'Jacopo\Authentication\Controllers\GroupController@editGroup']);
    Route::post('/admin/groups/edit', ["before" => "csrf", 'as' => 'groups.edit', 'uses' => 'Jacopo\Authentication\Controllers\GroupController@postEditGroup']);
    Route::get('/admin/groups/delete', ["before" => "csrf", 'as' => 'groups.delete', 'uses' => 'Jacopo\Authentication\Controllers\GroupController@deleteGroup']);
    Route::post('/admin/groups/editpermission', ["before" => "csrf", 'as' => 'groups.edit.permission', 'uses' => 'Jacopo\Authentication\Controllers\GroupController@editPermission']);

    // permissions
    Route::get('/admin/permissions/list', ['as' => 'permission.list', 'uses' => 'Jacopo\Authentication\Controllers\PermissionController@getList']);
    Route::get('/admin/permissions/edit', ['as' => 'permission.edit', 'uses' => 'Jacopo\Authentication\Controllers\PermissionController@editPermission']);
    Route::post('/admin/permissions/edit', ["before" => "csrf", 'as' => 'permission.edit', 'uses' => 'Jacopo\Authentication\Controllers\PermissionController@postEditPermission']);
    Route::get('/admin/permissions/delete', ["before" => "csrf", 'as' => 'permission.delete', 'uses' => 'Jacopo\Authentication\Controllers\PermissionController@deletePermission']);

});

if (Config::get('authentication::handle_404'))
{
    App::missing(function ($exception){
        return View::make('authentication::client.exceptions.404');
    });
}