<?php

declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * @mixin think\Model
 */
class Post extends Model
{
  //

  use SoftDelete;

  protected $defaultSoftDelete = 0;
}
