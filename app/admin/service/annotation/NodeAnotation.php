<?php

// +----------------------------------------------------------------------
// | ulthon_admin
// +----------------------------------------------------------------------
// | PHP交流群: 207160418
// +----------------------------------------------------------------------
// | 开源协议  http://license.coscl.org.cn/MulanPSL2
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zhongshaofa/ulthon_admin
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
