<?php

declare(strict_types=1);

namespace app\index\controller;

use app\model\Post as ModelPost;
use think\Request;

class Post extends Common
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

    $model_post = ModelPost::find($id);

    $this->assign('post', $model_post);

    return $this->fetch();
  }

  public function __documentsRead()
  {
    $category_id = $this->request->param('category_id',0);

    $list_post = [];
    if(!empty($category_id)){
      $list_post = ModelPost::hasWhere('categorys',['category_id'=>$category_id])->order('sort desc')->select();
    }

    $this->assign('list_post',$list_post);

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
