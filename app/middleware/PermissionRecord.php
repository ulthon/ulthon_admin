<?php

namespace app\middleware;

use app\model\AdminPermission;
use app\Request;

class PermissionRecord
{
  public function handle(Request $request, \Closure $next)
  {

    return $next($request);
  }
}
