<?php

namespace base\common\model;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 有关时间的模型
 * Class TimeModel.
 */
class TimeModelBase extends BaseModel
{
    /**
     * 自动时间戳类型.
     * @var string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 添加时间.
     * @var string
     */
    protected $createTime = 'create_time';

    /**
     * 更新时间.
     * @var string
     */
    protected $updateTime = 'update_time';

    /**
     * 软删除.
     */
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    protected $defaultSoftDelete = 0;
}
