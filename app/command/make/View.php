<?php

declare(strict_types=1);

namespace app\command\make;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\App;

class View extends Command
{

  protected $originalNameData = [];

  protected function configure()
  {
    // 指令配置
    $this->setName('make:view')
      ->addArgument('name', Argument::REQUIRED, "The name of the class")
      ->addOption('controller', null, Option::VALUE_OPTIONAL, 'Generate an api controller class.')
      ->addOption('action', null, Option::VALUE_OPTIONAL, 'Generate an empty controller class.')
      ->setDescription('从模板生成view文件,用法: 应用名@[目录/]控制器名/方法名,目录可以为空,要求对应应用目录下存在/common/tpl.html,建议根据文档风格要求,采用小写加下划线方式命名');
  }

  protected function execute(Input $input, Output $output)
  {

    $name = $input->getArgument('name');

    if(empty($name)){
      $output->writeln('请传入文件名称');
      return false;
    }
    $name_patt = "/\w+@(\w+\/)+\w+/";
    
    if(!preg_match($name_patt,$name)){
      
      $output->writeln('传入参数不正确,用法: 应用名@[目录/]控制器名/方法名,目录可以为空,无论设置的分隔符是什么,都要用/,生成时会替换成设置的分隔符');
      return false;
    }

    $name_arr = explode('@',$name);

    $this->originalNameData['appname'] = $appname = $name_arr[0];

    $this->originalNameData['path'] = $path = $name_arr[1];

    $view_suffix = config('view.view_suffix');

    $view_depr = config('view.view_depr');

    $base_view_dir = App::getRootPath().config('view.view_dir_name');

    $app_view_dir = $base_view_dir.$view_depr.$appname;

    $tpl_file_path = $app_view_dir.$view_depr.'common'. $view_depr.'tpl.'. $view_suffix;

    if(!is_file($tpl_file_path)){

      $output->writeln('模板文件不存在:'.$tpl_file_path.',请先创建该文件');

      return false;
    }

    $target_file_path = $app_view_dir.$view_depr.$path;

    $target_file_path = $target_file_path.'.'. $view_suffix;

    $target_file_path = str_replace('/',$view_depr,$target_file_path);

    if(is_file($target_file_path)){
      
      $output->writeln('目标文件已存在:'.$target_file_path);

      return false;
    }

  

    $tpl_file_content = file_get_contents($tpl_file_path);

    $tpl_file_content = $this->optionsReplace($tpl_file_content);
    


    $target_file_dir = dirname($target_file_path);

    if(!is_dir($target_file_dir)){
      mkdir($target_file_dir,0777,true);
    }



    file_put_contents($target_file_path,$tpl_file_content);

    $output->writeln('创建文件成功:'.$target_file_path);
  }

  public function optionsReplace($content)
  {
    $controller = $this->input->getOption('controller');

    if(empty($controller)){
      $controller = \think\helper\Str::studly(array_slice(explode('/',$this->originalNameData['path']),-2,1)[0]);
    }
    if(empty($action)){
      $action = array_slice(explode('/',$this->originalNameData['path']),-1,1)[0];
    }

    $content = str_replace('{%controllerName%}',$controller,$content);
    $content = str_replace('{%actionName%}',$action,$content);

    return $content;
  }
}
