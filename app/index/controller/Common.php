<?php

namespace app\index\controller;

use app\model\Category;
use app\model\Nav;
use app\model\Post;
use think\facade\View;
use think\helper\Str;

class Common extends BaseController
{
  public function initialize()
  {
    parent::initialize();


    $list_nav_slide = Nav::where('type', 3)->order('sort asc')->where('status', 1)->select();
    View::assign('list_nav_slide', $list_nav_slide);
    $list_nav_friend_url = Nav::where('type', 2)->order('sort asc')->where('status', 1)->select();
    View::assign('list_nav_friend_url', $list_nav_friend_url);

    if (!empty($this->indexTplMethod)) {
      if (method_exists($this, $this->indexTplMethod)) {
        $this->{$this->indexTplMethod}();
      }
    }
    if (!empty($this->indexTplMethodCurrentAction)) {
      if (method_exists($this, $this->indexTplMethodCurrentAction)) {
        $this->{$this->indexTplMethodCurrentAction}();
      }
    }
  }

  public function __blog()
  {
    $list_header_nav = Nav::where('type', 'blog_header_nav')->order('sort asc')->where('status', 1)->select();
    View::assign('list_header_nav', $list_header_nav);
  }

  public function __documents()
  {
    $list_header_nav = Nav::where('type', 'document_header_nav')->order('sort asc')->where('status', 1)->select();
    View::assign('list_header_nav', $list_header_nav);
  }

  public function __easyBlue()
  {
    $list_header_nav = Nav::where('type', 10)->order('sort asc')->where('status', 1)->select();
    View::assign('list_header_nav', $list_header_nav);
    $list_nav_index_block_1 = Nav::where('type', 6)->order('sort asc')->where('status', 1)->select();
    View::assign('list_nav_index_block_1', $list_nav_index_block_1);
    $list_nav_index_block_2 = Nav::where('type', 7)->order('sort asc')->where('status', 1)->select();
    View::assign('list_nav_index_block_2', $list_nav_index_block_2);
  }

  public function __articles()
  {
    $list_header_nav = Nav::where('type', 11)->order('sort asc')->where('status', 1)->select();
    View::assign('list_header_nav', $list_header_nav);

    $list_category_first_level = Category::where('level', 1)->where('status', 1)->where('type',3)->select();
    $this->assign('list_category_first_level', $list_category_first_level);
    $list_nav_more = Nav::where('type', 8)->order('sort asc')->where('status', 1)->where('type',11)->select();
    View::assign('list_nav_more', $list_nav_more);

    $top_posts = Post::where('is_top',1)->limit(8)->where('type',3)->select();
    $this->assign('top_posts',$top_posts);

  }
}
