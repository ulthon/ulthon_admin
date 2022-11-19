<?php

namespace app\admin\controller\mall;

use app\common\controller\AdminController;

use think\App;

/**
 * @\app\admin\service\annotation\ControllerAnnotation(title="mall_tag")
 */
class Tag extends AdminController
{

    use \app\admin\traits\Curd;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\admin\model\MallTag();
        
    }

    
}