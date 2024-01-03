<?php

namespace base\common\interface\test;

use think\console\Input;
use think\console\Output;

/**
 * 测试类接口.
 *
 * 实现了该接口的类，会在执行命令时，自动执行该接口的方法，比如传入参数，输出结果等。
 * 要注意的是，并不代表该接口只能在命令行中使用，也可以在其他地方使用，在其他地方使用时，传入不同“实现”的output和input。例如在控制器中使用，传入的output会输出到Response。
 */
interface CommandTestInterfaceBase
{
    public function setOutput(Output $output);

    public function setInput(Input $input);
}
