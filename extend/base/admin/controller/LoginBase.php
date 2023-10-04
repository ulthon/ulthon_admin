<?php

namespace base\admin\controller;

use app\admin\model\SystemAdmin;
use app\common\controller\AdminController;
use think\captcha\facade\Captcha;
use think\facade\Env;
use think\facade\Event;
use think\facade\Session;

/**
 * Class Login.
 */
class LoginBase extends AdminController
{
    /**
     * 初始化方法.
     */
    public function initialize()
    {
        parent::initialize();
        $action = $this->request->action();
        if (!empty(session('admin')) && !in_array($action, ['out'])) {
            $adminModuleName = config('app.admin_alias_name');
            $this->success('已登录，无需再次登录', [], __url("@{$adminModuleName}"));
        }
    }

    /**
     * 用户登录.
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        event_response('AdminLoginIndex', [
            'controller' => $this,
        ]);

        $back_url = $this->request->param('back_url');

        if (!empty($back_url)) {
            Session::set('back-url', $back_url);
        } else {
            $back_url = Session::get('back-url');
        }

        if (empty($back_url)) {
            $back_url = null;
        }

        $captcha = Env::get('adminsystem.captcha', 1);
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $rule = [
                'username|用户名' => 'require',
                'password|密码' => 'require',
                'keep_login|是否保持登录' => 'require',
            ];
            $captcha == 1 && $rule['captcha|验证码'] = 'require|captcha';
            $this->validate($post, $rule);
            $admin = SystemAdmin::where(['username' => $post['username']])->find();
            if (empty($admin)) {
                $this->error('用户不存在');
            }
            if (password($post['password']) != $admin->password) {
                $this->error('密码输入有误');
            }
            if ($admin->status == 0) {
                $this->error('账号已被禁用');
            }
            $admin->login_num += 1;
            $admin->save();

            Event::trigger('AdminLoginSuccess', $admin);

            $admin = $admin->toArray();
            unset($admin['password']);
            $admin['expire_time'] = $post['keep_login'] == 1 ? true : time() + 7200;
            session('admin', $admin);

            Session::delete('back-url');
            $this->success('登录成功', '', $back_url);
        }
        $this->assign('captcha', $captcha);
        $this->assign('demo', $this->isDemo);

        return $this->fetch();
    }

    /**
     * 用户退出.
     * @return mixed
     */
    public function out()
    {
        session('admin', null);
        $this->success('退出登录成功');
    }

    /**
     * 验证码
     * @return \think\Response
     */
    public function captcha()
    {
        return Captcha::create();
    }
}
