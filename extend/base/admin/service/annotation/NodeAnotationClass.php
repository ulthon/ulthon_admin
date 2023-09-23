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

namespace base\admin\service\annotation;

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
class NodeAnotationClass
{
    /**
     * 节点名称.
     * @Required()
     * @var string
     */
    public $title;

    /**
     * 是否开启权限控制.
     * @Enum({true,false})
     * @var bool
     */
    public $auth = true;

    /**
     * 节点 一般无需设置.
     * @var string
     */
    public $name;
}
