<?php

// +----------------------------------------------------------------------
// | EasyAdmin
// +----------------------------------------------------------------------
// | PHP交流群: 763822524
// +----------------------------------------------------------------------
// | 开源协议  https://mit-license.org
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zhongshaofa/EasyAdmin
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
