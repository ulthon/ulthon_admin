<?php

namespace app\admin\controller\system;

use app\admin\service\annotation\ControllerAnnotation;
use app\admin\service\annotation\NodeAnotation;
use base\admin\controller\system\AdminBase;

/**
 * Class Admin.
 * @ControllerAnnotation(title="管理员管理")
 *
 * @NodeAnotation(title="自定义权限标识符",name="customFlag")
 */
class Admin extends AdminBase
{
}
