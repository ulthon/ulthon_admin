<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\model\Nav as ModelNav;
use think\facade\View;
use think\Request;

class Nav extends Common
{
  /**
   * 显示资源列表
   *
   * @return \think\Response
   */
  public function index(Request $request)
  {
    //
    $type = $request->param('type',1);

    $list = ModelNav::order('sort asc')->order('id asc')->where('type',$type)->select();

    View::assign('type', $type);
    View::assign('list', $list);
    return View::fetch();
  }

  /**
   * 显示创建资源表单页.
   *
   * @return \think\Response
   */
  public function create()
  {
    //

    return View::fetch();
  }

  /**
   * 保存新建的资源
   *
   * @param  \think\Request  $request
   * @return \think\Response
   */
  public function save(Request $request)
  {
    //
    $post_data = $request->post();

    ModelNav::create($post_data);

    return $this->success('添加成功', url('index',[
      'type'=>$request->param('type',1),
      'show_img'=>$request->param('show_img',0),
      'show_target'=>$request->param('show_target',0),
      'show_xcx'=>$request->param('show_xcx',0),
    ]));
  }

  /**
   * 显示指定的资源
   *
   * @param  int  $id
   * @return \think\Response
   */
  public function read($id)
  {
    //
  }

  /**
   * 显示编辑资源表单页.
   *
   * @param  int  $id
   * @return \think\Response
   */
  public function edit($id)
  {
    //

    $model_nav = ModelNav::find($id);


    View::assign('nav', $model_nav);
    return View::fetch();
  }

  /**
   * 保存更新的资源
   *
   * @param  \think\Request  $request
   * @param  int  $id
   * @return \think\Response
   */
  public function update(Request $request, $id)
  {
    //
    $post_data = $request->post();
    $model_nav = ModelNav::find($id);

    $model_nav->save($post_data);

    return $this->success('保存成功', url('index',[
      'type'=>$model_nav->getData('type',1),
      'show_img'=>$request->param('show_img',0),
      'show_target'=>$request->param('show_target',0),
      'show_xcx'=>$request->param('show_xcx',0),
    ]));
  }

  /**
   * 删除指定资源
   *
   * @param  int  $id
   * @return \think\Response
   */
  public function delete($id)
  {
    //

    ModelNav::destroy($id);

    return $this->success("删除成功");
  }
}
