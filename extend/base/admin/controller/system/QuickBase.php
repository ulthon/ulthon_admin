<?php

namespace base\admin\controller\system;

use app\admin\model\SystemQuick;
use app\admin\service\annotation\ControllerAnnotation;
use app\common\controller\AdminController;
use think\App;

/**
 * @ControllerAnnotation(title="快捷入口管理")
 * Class Quick
 */
class QuickBase extends AdminController
{
    use \app\admin\traits\Curd;

    protected $sort = [
        'sort' => 'desc',
        'id' => 'desc',
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new SystemQuick();
    }
}
