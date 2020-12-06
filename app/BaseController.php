<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app;

use think\App;
use think\app\Url;
use think\exception\ValidateException;
use think\Validate;
use think\facade\View;
use think\exception\HttpResponseException;

/**
 * 控制器基础类
 */
abstract class BaseController
{
  /**
   * Request实例
   * @var \think\Request
   */
  protected $request;

  /**
   * 应用实例
   * @var \think\App
   */
  protected $app;

  /**
   * 是否批量验证
   * @var bool
   */
  protected $batchValidate = false;

  /**
   * 控制器中间件
   * @var array
   */
  protected $middleware = [];

  /**
   * 构造方法
   * @access public
   * @param  App  $app  应用对象
   */
  public function __construct(App $app)
  {
    $this->app     = $app;
    $this->request = $this->app->request;

    // 控制器初始化
    $this->initialize();
  }

  // 初始化
  protected function initialize()
  {
  }

  /**
   * 验证数据
   * @access protected
   * @param  array        $data     数据
   * @param  string|array $validate 验证器名或者验证规则数组
   * @param  array        $message  提示信息
   * @param  bool         $batch    是否批量验证
   * @return array|string|true
   * @throws ValidateException
   */
  protected function validate(array $data, $validate, array $message = [], bool $batch = false)
  {
    if (is_array($validate)) {
      $v = new Validate();
      $v->rule($validate);
    } else {
      if (strpos($validate, '.')) {
        // 支持场景
        list($validate, $scene) = explode('.', $validate);
      }
      $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
      $v     = new $class();
      if (!empty($scene)) {
        $v->scene($scene);
      }
    }

    $v->message($message);

    // 是否批量验证
    if ($batch || $this->batchValidate) {
      $v->batch(true);
    }

    return $v->failException(true)->check($data);
  }

  public function success($msg = '操作成功', $jump_to_url = null, $code = 200, $params = [])
  {
    $jump_to_url = $this->parseJumpUrl($jump_to_url);
    $data = [
      'msg' => $msg,
      'jump_to_url' => $jump_to_url,
      'params' => $params
    ];

    if (\request()->isAjax()) {
      $data['jump_to_url'] = $jump_to_url;
      if ($code == 200) {
        $code = 0;
      }
      throw new HttpResponseException(json_message($data, $code, $msg));
    }

    View::assign($data);
    throw new HttpResponseException(response(View::fetch('common@tpl/success'), $code));
  }
  public function error($msg = '操作失败', $jump_to_url = null, $code = 200, $params = [])
  {

    $jump_to_url = $this->parseJumpUrl($jump_to_url);

    $data = [
      'msg' => $msg,
      'jump_to_url' => $jump_to_url,
      'params' => $params
    ];

    if (\request()->isAjax()) {
      $data['jump_to_url'] = $jump_to_url;
      if ($code == 200) {
        $code = 500;
      }
      throw new HttpResponseException(json_message($data, $code, $msg));
    }

    View::assign($data);
    throw new HttpResponseException(response(View::fetch('common@tpl/error'), $code));
  }

  public function redirect($jump_to_url, $code = 302)
  {
    $jump_to_url = $this->parseJumpUrl($jump_to_url);

    throw new HttpResponseException(redirect($jump_to_url), $code);
  }

  public function parseJumpUrl($jump_to_url)
  {
    if (is_null($jump_to_url)) {
      $jump_to_url = \request()->server('HTTP_REFERER');
    } else {
      if ($jump_to_url instanceof Url) {

        $jump_to_url = (string)$jump_to_url;
      } else {
        if (strpos($jump_to_url, 'http') !== 0) {
          $jump_to_url = url($jump_to_url);
        }
      }
    }

    return (string)$jump_to_url;
  }
}
