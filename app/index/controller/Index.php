<?php

namespace app\index\controller;

use app\model\Category;
use app\model\Nav;
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

  public function __blogIndex()
  {
    $list_category = Category::where('type','blog_post')->select();

    $this->assign('list_category',$list_category);

    $category_id = $this->request->param('category_id');

    if(!empty($category_id)){

      $model_list_post = Post::hasWhere('categorys',['category_id'=>$category_id])->order('sort desc');
    }else{
      
      $model_list_post = Post::order('sort desc');
    }

    $model_list_post->where('type','blog_post');

    $list_post = $model_list_post->paginate();

    $this->assign('list_post',$list_post);
    

  }

  public function __documentsIndex()
  {
    $list_index_documents_nav = Nav::where('type',9)->select();

    $this->assign('list_index_documents_nav',$list_index_documents_nav);
  }

  public function __articlesIndex()
  {

    $sub_category = [];

    if(!empty($this->request->param('category_id'))){
      $sub_category = Category::where('pid',$this->request->param('category_id'))->where('type',3)->select();

      if(empty($this->request->param('sub_category_id'))){
        $categorys = [$this->request->param('category_id')];

        $categorys = array_merge($categorys,array_column((array)Category::getListLevel($this->request->param('category_id')),3));

        $categorys_where = PostCategory::whereIn('category_id',$categorys);

        $model_post = Post::hasWhere('categorys',$categorys_where)->where('status',1)->order('id desc');
      }else{
        $model_post = Post::hasWhere('categorys',['category_id'=>$this->request->param('sub_category_id')])->where('status',1)->order('id desc');

      }
    }else{

      $model_post = Post::where('status',1)->order('id desc');
    }
    
    $model_post->where('type',3);

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
