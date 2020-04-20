<?php

namespace app\index\controller;

use app\BaseController as AppBaseController;
use think\facade\Config;
use think\facade\View;
use think\helper\Str;

class BaseController extends AppBaseController
{

  /**
   * 是否使用多模板
   * 仅当名称为空或者指定名称有效,
   * 使用跨应用,跨控制器,引用模板路径的写法时无效
   *
   * @var boolean
   */
  protected $isUseTpls = true;

  protected $indexTplName = '';
  protected $indexTplMethod = '';
  protected $indexTplMethodCurrentAction = '';



  public function initialize()
  {
    parent::initialize();

    $this->indexTplName = get_system_config('index_tpl_name');

    $this->indexTplMethod = '__'.Str::camel($this->indexTplName);

    $this->indexTplMethodCurrentAction = $this->indexTplMethod.Str::studly($this->request->action());
    
    
  }

  public function assign($template, $value)
  {
    return View::assign($template, $value);
  }

  public function fetch($template = '', $vars = [])
  {
    if ($this->isUseTpls && strpos($template, '@') === false && stripos($template, '/') === false) {

      if ($template === '') {
        $config_auto_rule = Config::get('view.auto_rule');
        if (2 == $config_auto_rule) {
          $template = $this->request->action(true);
        } elseif (3 == $config_auto_rule) {
          $template = $this->request->action();
        } else {
          $template = Str::snake($this->request->action());
        }
      }


      return View::fetch($this->indexTplName . $template, $vars);
    } else {
      return View::fetch($template, $vars);
    }
  }
}
