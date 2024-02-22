<?php

namespace base\common\tools;

use think\helper\Arr;

/**
 * 全局数据存储工具.
 *
 * 可以在当前生命周期内，存储一些全局数据，
 * 相比SESSION，性能更高，但是只能在当前生命周期内使用（常驻内存服务时除外）
 */
class StoreValueToolsBase
{
    protected static $store = [];

    public static function set($key, $value)
    {
        return Arr::set(static::$store, $key, $value);
    }

    public static function get($key, $default = null)
    {
        return Arr::get(static::$store, $key, $default);
    }

    public static function __callStatic($name, $arguments)
    {
        $arguments = array_merge([static::$store], $arguments);

        return call_user_func_array([Arr::class, $name], $arguments);
    }
}
