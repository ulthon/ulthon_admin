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

use base\admin\service\annotation\NodeAnotationBase;
use Doctrine\Common\Annotations\Annotation\Attributes;

/**
 * 创建节点注解类.
 *
 * @Annotation
 * @Target({"METHOD","CLASS"})
 * @Attributes({
 *   @Attribute("time", type = "int")
 * })
 */
final class NodeAnotation extends NodeAnotationBase
{
}
