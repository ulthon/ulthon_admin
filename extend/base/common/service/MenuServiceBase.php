<?php

namespace base\common\service;

use app\common\constants\MenuConstant;
use app\common\service\AuthService;
use think\facade\Db;

class MenuServiceBase
{
    /**
     * 管理员ID.
     * @var int
     */
    protected $adminId;

    public function __construct($adminId)
    {
        $this->adminId = $adminId;

        return $this;
    }

    /**
     * 获取首页信息.
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getHomeInfo()
    {
        $data = Db::name('system_menu')
            ->field('title,icon,href')
            ->where('delete_time', 0)
            ->where('pid', MenuConstant::HOME_PID)
            ->find();
        !empty($data) && $data['href'] = __url($data['href']);

        return $data;
    }

    /**
     * 获取后台菜单树信息.
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMenuTree()
    {
        /** @var AuthService $authService */
        $authServer = app(AuthService::class, ['adminId' => $this->adminId]);

        return $this->buildMenuChild(0, $this->getMenuData(), $authServer);
    }

    private function buildMenuChild($pid, $menuList, AuthService $authServer)
    {
        $treeList = [];
        foreach ($menuList as &$v) {
            $check = false;
            if (!empty($v['auth_node'])) {
                $check = $authServer->checkNode($v['auth_node']);
            } elseif (!empty($v['href'])) {
                $check = $authServer->checkNode($v['href']);
            } else {
                $check = true;
            }

            !empty($v['href']) && $v['href'] = __url($v['href']);
            if ($pid == $v['pid'] && $check) {
                $node = $v;
                $child = $this->buildMenuChild($v['id'], $menuList, $authServer);
                if (!empty($child)) {
                    $node['child'] = $child;
                }
                if (!empty($v['href']) || !empty($child)) {
                    $treeList[] = $node;
                }
            }
        }

        return $treeList;
    }

    /**
     * 获取所有菜单数据.
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function getMenuData()
    {
        $menuData = Db::name('system_menu')
            ->field('id,pid,title,icon,href,target')
            ->where('delete_time', 0)
            ->where([
                ['status', '=', '1'],
                ['pid', '<>', MenuConstant::HOME_PID],
            ])
            ->order([
                'sort' => 'desc',
                'id' => 'asc',
            ])
            ->select();

        return $menuData;
    }
}
