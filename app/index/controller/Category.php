<?php

declare(strict_types=1);

namespace app\index\controller;

use app\model\Category as ModelCategory;
use think\Request;

class Category extends Common
{
  /**
   * 显示资源列表
   *
   * @return \think\Response
   */
  public function index()
  {
    //
  }

  /**
   * 显示创建资源表单页.
   *
   * @return \think\Response
   */
  public function create()
  {
    //
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

    $model_category = ModelCategory::with('posts.post')->find($id);
    
    $this->assign('category',$model_category);
    
    return $this->fetch('read'.$model_category->tpl_name);
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
  }
}
