<?php

// +----------------------------------------------------------------------
// | ulthon_admin
// +----------------------------------------------------------------------
// | PHP交流群: 207160418
// +----------------------------------------------------------------------
// | 开源协议  http://license.coscl.org.cn/MulanPSL2
// +----------------------------------------------------------------------
// | gitee开源项目：https://gitee.com/ulthon/ulthon_admin
// +----------------------------------------------------------------------

namespace base\admin\service\annotation;

use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class ControllerAnnotation.
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("title", type="string"),
 * })
 *
 * @since 2.0
 */
class ControllerAnnotationBase
{
    /**
     * Route group prefix for the controller.
     *
     * @Required()
     *
     * @var string
     */
    public $title = '';

    /**
     * 是否开启权限控制.
     * @Enum({true,false})
     * @var bool
     */
    public $auth = true;
}
