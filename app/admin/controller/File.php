<?php

namespace app\admin\controller;

use app\model\UploadFiles;
use app\UploadFiles as AppUploadFiles;
use think\facade\View;
use think\Request;

class File extends Common
{
  /**
   * 显示资源列表
   *
   * @return \think\Response
   */
  public function index()
  {
    //

    $type = $this->request->param('type', 1);
    $status = $this->request->param('status', '');

    $model_list = UploadFiles::withTrashed()->where('type', $type)->order('id desc');

    if ($status != '') {
      $model_list->where('status', $status);
    }

    $list = $model_list->paginate();
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

    return AppUploadFiles::save($request);
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

  public function clear($id)
  {
    AppUploadFiles::clear($id);

    return json_message();
  }
}
