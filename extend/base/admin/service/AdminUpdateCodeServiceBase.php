<?php

namespace base\admin\service;

use think\console\Input;
use think\console\Output;
use UpdateFunction;

class AdminUpdateCodeServiceBase
{
    /**
     * @var Input
     */
    public $input;

    /**
     * @var Output
     */
    public $output;

    public function update($version)
    {
        $class_function_path = app_file_path('admin/service/adminUpdateCodeData/' . $version . '.php');

        if (!file_exists($class_function_path)) {
            $this->output->error('指定版本无需特定更新');

            return;
        }

        require_once $class_function_path;

        $update_class = new UpdateFunction();
        $update_class->input = $this->input;
        $update_class->output = $this->output;
        $update_class->update();
    }
}
