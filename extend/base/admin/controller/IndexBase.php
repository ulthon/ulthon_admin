<?php

namespace base\admin\controller;

use app\admin\model\SystemAdmin;
use app\admin\model\SystemMenu;
use app\admin\model\SystemQuick;
use app\common\controller\AdminController;
use app\common\service\MenuService;

class IndexBase extends AdminController
{
    /**
     * 后台主页.
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        return $this->fetch('', [
            'admin' => session('admin'),
        ]);
    }

    /**
     * 后台欢迎页.
     * @return string
     * @throws \Exception
     */
    public function welcome()
    {
        $quicks = SystemQuick::field('id,title,icon,href')
            ->where(['status' => 1])
            ->order('sort', 'desc')
            ->autoCache('welcome_list')
            ->limit(8)
            ->select();
        $this->assign('quicks', $quicks);

        return $this->fetch();
    }

    /**
     * 修改管理员信息.
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editAdmin()
    {
        $id = session('admin.id');
        $row = (new SystemAdmin())
            ->withoutField('password')
            ->find($id);
        empty($row) && $this->error('用户信息不存在');
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $this->isDemo && $this->error('演示环境下不允许修改');
            $rule = [];
            $this->validate($post, $rule);
            try {
                $save = $row
                    ->allowField(['head_img', 'phone', 'remark', 'update_time'])
                    ->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败');
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        $this->assign('row', $row);

        return $this->fetch();
    }

    /**
     * 修改密码
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editPassword()
    {
        $id = session('admin.id');
        $row = (new SystemAdmin())
            ->withoutField('password')
            ->find($id);
        if (!$row) {
            $this->error('用户信息不存在');
        }
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $this->isDemo && $this->error('演示环境下不允许修改');
            $rule = [
                'password|登录密码' => 'require',
                'password_again|确认密码' => 'require',
            ];
            $this->validate($post, $rule);
            if ($post['password'] != $post['password_again']) {
                $this->error('两次密码输入不一致');
            }

            try {
                $save = $row->save([
                    'password' => password($post['password']),
                ]);
            } catch (\Exception $e) {
                $this->error('保存失败');
            }
            if ($save) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $this->assign('row', $row);

        return $this->fetch();
    }

    public function mobile()
    {
        $pid = $this->request->param('pid', 0);

        $menuService = new MenuService(session('admin.id'));

        $home_info = $menuService->getHomeInfo();

        $list_menu = SystemMenu::with(['children' => function ($query) {
            $query->order('sort', 'desc')->order('id', 'asc');
        }])->where('pid', $pid)->order([
            'sort' => 'desc',
            'id' => 'asc',
        ])
        ->where('status', 1)
        ->select();

        $list_menu_pid = SystemMenu::group('pid')->column('pid');

        foreach ($list_menu as $model_menu) {
            foreach ($model_menu->children as $model_child) {
                if (in_array($model_child->id, $list_menu_pid)) {
                    $model_child->href = __url('mobile', ['pid' => $model_child->pid]);
                } else {
                    $model_child->href = __url($model_child->href);
                }
            }
        }

        $this->assign('home_info', $home_info);
        $this->assign('list_menu', $list_menu);

        return $this->fetch();
    }
}
