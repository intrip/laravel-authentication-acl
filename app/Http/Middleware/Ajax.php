<?php  namespace LaravelAcl\Http\Middleware; 

use Closure;
use Illuminate\Support\Facades\App;

/*
 * Check that request type is ajax
 */
class Ajax {

    public function handle($request, Closure $next)
    {
        if(!Request::ajax()) {
            return Response::error('404');
        }

        return $next($request);
    }
} 