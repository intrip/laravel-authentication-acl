<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Jacopo\Authentication\Helpers\FileRouteHelper;

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
*/

/*
 * Check that the current user is logged and active and redirect to admin login
 */
Route::filter('admin_logged', function () {
    if(!App::make('authenticator')->check()) return Redirect::to('/admin/login');
});

/*
 * Check that the current user is logged and active and redirect to client login or
 * to custom url if given
 */
Route::filter('logged', function ($request, $response, $custom_url = null) {
    $redirect_url = $custom_url ?: '/login';
    if(!App::make('authenticator')->check()) return Redirect::to($redirect_url);
});

/*
|--------------------------------------------------------------------------
| Permission Filters
|--------------------------------------------------------------------------
|
| Check that the current user is logged in and has a the permission corresponding to the config menu file
|
*/

Route::filter('can_see', function () {
    $route_helper = new FileRouteHelper;
    if(!$route_helper->hasPermForRoute(Route::currentRouteName())) App::abort('401');
});

/*
 * Check that the user has one of the permission given
 */
Route::filter('has_perm', function () {
    $permissions = array_slice(func_get_args(), 2);

    $authentication_helper = App::make('authentication_helper');
    if(!$authentication_helper->hasPermission($permissions)) App::abort('401');
});

/*
|--------------------------------------------------------------------------
| Other Filters
|--------------------------------------------------------------------------
|
*/

Route::filter('ajax', function () {
    if(!Request::ajax()) {
        return Response::error('404');
    }
});