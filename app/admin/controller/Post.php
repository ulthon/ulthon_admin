<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\model\Category;
use app\model\Post as ModelPost;
use app\model\PostCategory;
use app\model\PostTag;
use app\model\Tag;
use think\facade\View;
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

    $list = ModelPost::with(['categorys.category','tags.tag'])
    ->where('type',$this->request->param('type',1))
    ->order('id desc')
    ->paginate();

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

    $categorys = [];
    $tags = [];
    if (isset($post_data['categorys'])) {
      $categorys = $post_data['categorys'];
      unset($post_data['categorys']);
    }
    if (isset($post_data['tags'])) {
      $tags = $post_data['tags'];
      unset($post_data['tags']);
    }

    $model_post = ModelPost::create($post_data);

    foreach ($categorys as $category) {
      PostCategory::create([
        'post_id' => $model_post->id,
        'category_id' => $category
      ]);
    }
    foreach ($tags as $tag) {
      PostTag::create([
        'post_id' => $model_post->id,
        'tag_id' => $tag
      ]);
    }

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

    $model_post = ModelPost::find($id);


    View::assign('post', $model_post);

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

    $model_post = ModelPost::find($id);

    $categorys = [];
    $tags = [];
    if (isset($post_data['categorys'])) {
      $categorys = $post_data['categorys'];
      unset($post_data['categorys']);
    }
    if (isset($post_data['tags'])) {
      $tags = $post_data['tags'];
      unset($post_data['tags']);
    }

    $model_post->save($post_data);

    $old_category_list = PostCategory::where('post_id', $id)->select();
    $old_category_id_list = array_column((array)$old_category_list, 'id');
    $old_tag_list = PostTag::where('post_id', $id)->select();
    $old_tag_id_list = array_column((array)$old_tag_list, 'id');

    // 旧的有新的没有
    foreach ($old_category_list as $model_category) {
      if (!in_array($model_category->id, $categorys)) {
        $model_category->delete();
      }
    }
    foreach ($old_tag_list as $model_tag) {
      if (!in_array($model_tag->id, $tags)) {
        $model_tag->delete();
      }
    }


    // 旧的没有新的有
    foreach ($categorys as $category) {
      if (!in_array($category, $old_category_id_list)) {

        PostCategory::create([
          'post_id' => $model_post->id,
          'category_id' => $category
        ]);
      }
    }

    foreach ($tags as $tag) {
      if (!in_array($tag, $old_tag_id_list)) {

        PostTag::create([
          'post_id' => $model_post->id,
          'tag_id' => $tag
        ]);
      }
    }

    return $this->success('保存成功', url('index',['type'=>$model_post->getData('type')]));
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

    $model_post = ModelPost::find($id);

    $model_post->delete();

    PostCategory::where('post_id',$id)->delete();

    PostTag::where('post_id',$id)->delete();

    return json_message();
  }
}
