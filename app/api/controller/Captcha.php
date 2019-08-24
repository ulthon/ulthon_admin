<?php

namespace app\api\controller;

use think\captcha\facade\Captcha as ThinkCaptcha;
use think\Request;

class Captcha
{
    public function build()
    {
        return ThinkCaptcha::create();
    }
}
