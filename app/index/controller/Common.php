<?php
namespace app\index\controller;

use app\model\Nav;
use think\facade\View;

class Common extends BaseController 
{
  public function initialize()
  {

    parent::initialize();

    $list_header_nav = Nav::where('type',1)->order('sort asc')->where('status',1)->select();
    View::assign('list_header_nav',$list_header_nav);
    $list_nav_slide = Nav::where('type',3)->order('sort asc')->where('status',1)->select();
    View::assign('list_nav_slide',$list_nav_slide);
    $list_nav_index_block_1 = Nav::where('type',6)->order('sort asc')->where('status',1)->select();
    View::assign('list_nav_index_block_1',$list_nav_index_block_1);
    $list_nav_index_block_2 = Nav::where('type',7)->order('sort asc')->where('status',1)->select();
    View::assign('list_nav_index_block_2',$list_nav_index_block_2);
    $list_nav_friend_url = Nav::where('type',2)->order('sort asc')->where('status',1)->select();
    View::assign('list_nav_friend_url',$list_nav_friend_url);
  }
}
