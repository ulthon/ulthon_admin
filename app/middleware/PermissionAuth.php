<?php

namespace app\middleware;

class PermissionAuth
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}
