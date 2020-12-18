<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\model\Category as ModelCategory;
use think\facade\View;
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

    $list = ModelCategory::getListLevel('',$this->request->param('type',1));

    if($this->request->isAjax()){
      return json_message($list);
    }

    View::assign('list',$list);

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

    $list = ModelCategory::getListLevel('',$this->request->param('type',1));

    View::assign('list_category',$list);

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

    if(empty($post_data['title'])){
      return $this->error('标题不能为空',null,500);
    }

    $model_category = ModelCategory::where('title',$post_data['title'])
    ->where('type',$post_data['type'])
    ->where('pid',$post_data['pid'])
    ->find();

    if(!empty($model_category)){
      $this->error('相同名称相同级别不能出现两次',null,500);
    }

    if($post_data['pid'] != 0){
      
      $model_parent_category = ModelCategory::where('id',$post_data['pid'])->find();
      
      $post_data['level'] = $model_parent_category->level + 1;

    }

    ModelCategory::create($post_data);

    return $this->success('添加成功',url('index',['type'=>$this->request->param('type')]));

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

    $model_category = ModelCategory::find($id);
    
    $list = ModelCategory::getListLevel('',$this->request->param('type',1));

    View::assign('list_category',$list);
    View::assign('category',$model_category);

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

    $model_category = ModelCategory::where('title',$post_data['title'])
    ->where('pid',$post_data['pid'])
    ->where('type',$post_data['type'])
    ->where('id','<>',$id)
    ->find();

    if(!empty($model_category)){
      $this->error('相同名称相同级别不能出现两次');
    }

    if($post_data['pid'] != 0){
      
      $model_parent_category = ModelCategory::where('id',$post_data['pid'])->find();
      
      $post_data['level'] = $model_parent_category->level + 1;

    }else{
      $post_data['level'] = 1;
    }

    $model_category = ModelCategory::find($id);

    $model_category->save($post_data);

    return $this->success('保存成功',url('index',['type'=>$model_category->getData('type')]));


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

    if($id == 0){
      return json_message('错误');
    }

    $model_category = ModelCategory::find($id);

    $pid = 0;

    if($model_category->pid != 0){

      $pid = $model_category->pid;
    }

    ModelCategory::where('pid',$id)->update(['pid'=>$pid]);

    $model_category->delete();

    return json_message();

  }
}
