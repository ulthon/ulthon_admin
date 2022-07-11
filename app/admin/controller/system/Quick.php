<?php


namespace app\admin\controller\system;


use app\admin\model\SystemQuick;
use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;

/**
 * @ControllerAnnotation(title="快捷入口管理")
 * Class Quick
 * @package app\admin\controller\system
 */
class Quick extends AdminController
{

    use \app\admin\traits\Curd;

    protected $sort = [
        'sort' => 'desc',
        'id'   => 'desc',
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new SystemQuick();
    }

}