<?php

namespace app\common\provider;

use think\View as ThinkView;

class View extends ThinkView
{
    /**
     * 设置布局
     * ! 注意，layout并非view类的标准方法，仅仅是think-view和think-template的特性方法
     * ! 但是，如果在provider中定制，则view的所有操作预期会发生错误
     * ! 同时，由于后台从设计支出就完全依赖think-view，所以专门为此扩展和特性定义方法时没有问题的.
     * @param bool|string $name    布局模板名称 false 则关闭布局
     * @param string      $replace 布局模板内容替换标识
     * @return $this
     */
    public function layout(bool|string $name, string $replace = ''): static
    {
        if (false === $name) {
            // 关闭布局
            $this->config(['layout_on' => false]);
        } else {
            // 开启布局
            $this->config(['layout_on' => true]);

            // 名称必须为字符串
            if (is_string($name)) {
                $this->config(['layout_name' => $name]);
            }

            if (!empty($replace)) {
                $this->config(['layout_item' => $replace]);
            }
        }

        return $this;
    }
}
