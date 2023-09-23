<?php

namespace base\admin\controller\mall;

use app\admin\model\MallCate;
use app\admin\service\annotation\ControllerAnnotation;
use app\admin\traits\Curd;
use app\common\controller\AdminController;
use think\App;

/**
 * Class Admin.
 * @ControllerAnnotation(title="商品分类管理")
 */
class CateBase extends AdminController
{
    use Curd;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new MallCate();
    }
}
