<?php

namespace base\admin\controller\debug;

use app\admin\service\annotation\ControllerAnnotation;
use app\common\controller\AdminController;
use think\App;

/**
 * @ControllerAnnotation(title="debug_log")
 */
class LogClass extends AdminController
{
    protected $sort = [
        'uid' => 'desc',
        'id' => 'asc',
    ];

    use \app\admin\traits\Curd;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\admin\model\DebugLog();
    }
}
