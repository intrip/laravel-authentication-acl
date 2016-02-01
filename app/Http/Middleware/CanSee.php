<?php  namespace LaravelAcl\Http\Middleware; 

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/*
 * Check that the current user is logged in and has a the permission corresponding
 * to the config menu file
 */
class CanSee {

    public function handle($request, Closure $next)
    {
        $route_helper = App::make('route_perm_helper');
        if(!$route_helper->hasPermForRoute(Route::currentRouteName())) App::abort('401');

        return $next($request);
    }
} 