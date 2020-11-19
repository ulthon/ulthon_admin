<?php

declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Nav extends Model
{

  protected static $listNav = [];

  public static $statusName = [
    0 => '不显示',
    1 => '显示'
  ];
  //
  public function getImgAttr($value)
  {
    return get_source_link($value, '/static/images/noimg.png');
  }

  public function getStatusNameAttr()
  {
    return self::$statusName[$this->getData('status')];
  }

  public static function getListNav($type = 1)
  {

    if (!isset(self::$listNav[$type])) {
      self::$listNav[$type] =  Nav::where('type', $type)->where('status', 1)->order('sort asc')->select();
    }
    return self::$listNav[$type];
  }
}
