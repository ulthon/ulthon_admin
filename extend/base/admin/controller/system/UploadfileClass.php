<?php

namespace base\admin\controller\system;

use app\admin\model\SystemUploadfile;
use app\admin\service\annotation\ControllerAnnotation;
use app\common\controller\AdminController;
use think\App;

/**
 * @ControllerAnnotation(title="上传文件管理")
 * Class Uploadfile
 */
class UploadfileClass extends AdminController
{
    use \app\admin\traits\Curd;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new SystemUploadfile();
    }
}
