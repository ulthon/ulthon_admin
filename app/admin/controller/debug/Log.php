<?php

namespace app\admin\controller\debug;

use app\common\controller\AdminController;

use think\App;

/**
 * @\app\admin\service\annotation\ControllerAnnotation(title="debug_log")
 */
class Log extends AdminController
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
