<?php

namespace base\admin\controller\mall;

use app\admin\service\annotation\ControllerAnnotation;
use app\common\controller\AdminController;
use think\App;

/**
 * @ControllerAnnotation(title="mall_tag")
 */
class TagBase extends AdminController
{
    use \app\admin\traits\Curd;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\admin\model\MallTag();
    }
}
