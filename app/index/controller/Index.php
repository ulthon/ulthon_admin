<?php

namespace app\index\controller;

use app\model\Category;
use app\model\Post;
use app\model\PostCategory;
use think\Request;

class Index extends Common
{
  /**
   * 显示资源列表
   *
   * @return \think\Response
   */
  public function index()
  {
    //

    return $this->fetch();
  }

  public function __articlesIndex()
  {

    $sub_category = [];

    if(!empty($this->request->param('category_id'))){
      $sub_category = Category::where('pid',$this->request->param('category_id'))->select();

      if(empty($this->request->param('sub_category_id'))){
        $categorys = [$this->request->param('category_id')];

        $categorys = array_merge($categorys,array_column((array)Category::getListLevel($this->request->param('category_id')),'id'));

        $categorys_where = PostCategory::whereIn('category_id',$categorys);

        $model_post = Post::hasWhere('categorys',$categorys_where)->where('status',1)->order('id desc');
      }else{
        $model_post = Post::hasWhere('categorys',['category_id'=>$this->request->param('sub_category_id')])->where('status',1)->order('id desc');

      }
    }else{

      $model_post = Post::where('status',1)->order('id desc');
    }
    
    $keywords = $this->request->param('keywords');

    if(!empty($keywords)){
      $model_post->whereLike('title|desc',"%$keywords%");
    }

    $list_post = $model_post->paginate();

    $this->assign('sub_category',$sub_category);

    $this->assign('list_post',$list_post);
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
