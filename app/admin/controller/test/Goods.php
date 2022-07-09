<?php

namespace app\admin\controller\test;

use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;

/**
 * @ControllerAnnotation(title="test_goods")
 */
class Goods extends AdminController
{

    use \app\admin\traits\Curd;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\admin\model\TestGoods();
        
        $this->assign('select_list_status', $this->model::SELECT_LIST_STATUS, true);

        $this->assign('select_list_time_status', $this->model::SELECT_LIST_TIME_STATUS, true);

        $this->assign('select_list_is_recommend', $this->model::SELECT_LIST_IS_RECOMMEND, true);

        $this->assign('select_list_shop_type', $this->model::SELECT_LIST_SHOP_TYPE, true);

    }

    
    /**
     * @NodeAnotation(title="列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            if (input('selectFields')) {
                return $this->selectList();
            }
            list($page, $limit, $where) = $this->buildTableParames();
            $count = $this->model
                ->withJoin('mallCate', 'LEFT')
                ->where($where)
                ->count();
            $list = $this->model
                ->withJoin('mallCate', 'LEFT')
                ->where($where)
                ->page($page, $limit)
                ->order($this->sort)
                ->select();
            $data = [
                'code'  => 0,
                'msg'   => '',
                'count' => $count,
                'data'  => $list,
            ];
            return json($data);
        }
        return $this->fetch();
    }
}