<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\model\Tag as ModelTag;
use think\facade\View;
use think\Request;

class Tag extends Common
{
  /**
   * 显示资源列表
   *
   * @return \think\Response
   */
  public function index()
  {
    //

    $list_tag = ModelTag::order('id desc')
    ->where('type',$this->request->param('type',1))
    ->paginate();

    if($this->request->isAjax()){
      return json_message($list_tag);
    }

    View::assign('list',$list_tag);

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

    $arr = explode(' ',$post_data['tags']);

    $arr = array_unique(array_filter($arr));

    foreach ($arr as $tag) {
      $model_tag = ModelTag::where('title',$tag)->find();

      if(empty($model_tag)){
        ModelTag::create(['title'=>$tag]);
      }
    }

    return json_message();
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

    $post_data['title'] = str_replace(' ','',$post_data['title']);
    
    $model_tag = ModelTag::find($id);

    $model_tag->save($post_data);

    return json_message();
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
