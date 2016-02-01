<?php  namespace LaravelAcl\Http\Middleware; 

use Closure;
use Illuminate\Support\Facades\App;

/*
 * Check that the user has one of the permission given
 */
class HasPerm {

    public function handle($request, Closure $next)
    {
        $permissions = array_slice(func_get_args(), 2);
        $authentication_helper = App::make('authentication_helper');
        if(!$authentication_helper->hasPermission($permissions)) App::abort('401');

        return $next($request);
    }
} 