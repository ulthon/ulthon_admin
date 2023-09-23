<?php

namespace app\common\event\AdminLoginIndex;

class Index
{
    public function handle($params)
    {
        $controller = $params['controller'];

        $controller->assign('captcha',1);

        return [
            'response' => $controller->fetch('login/ext/index'),
        ];
    }
}
