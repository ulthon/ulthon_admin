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

namespace app\admin\service\annotation;

use base\admin\service\annotation\ControllerAnnotationBase;
use Doctrine\Common\Annotations\Annotation\Attributes;
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
final class ControllerAnnotation extends ControllerAnnotationBase
{
}
