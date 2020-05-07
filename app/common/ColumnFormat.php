<?php
namespace app\common;

use think\migration\db\Column;

class ColumnFormat  
{
    public static function timestamp($name){
        return Column::make($name,'integer')
        ->setLimit(10)
        ->setSigned(false)
        ->setDefault(0);
    }

    public static function stringLong($name)
    {
        return Column::make($name,'string')
        ->setLimit(500)
        ->setDefault('');
    }
    public static function stringNormal($name)
    {
        return Column::make($name,'string')
        ->setLimit(100)
        ->setDefault('');
    }

    public static function stringUrl($name)
    {
        return Column::make($name,'string')
        ->setLimit(300)
        ->setDefault('');
    }

    public static function stringMd5($name)
    {
        return Column::make($name,'string')
        ->setLimit(32)
        ->setDefault('');
    }

    public static function stringShort($name)
    {
        return Column::make($name,'string')
        ->setLimit(20)
        ->setDefault('');
    }

    public static function integerTypeStatus($name,$default = 0)
    {
        return Column::make($name,'integer')
        ->setLimit(10)
        ->setSigned(false)
        ->setDefault($default);
    }

    public static function integer($name)
    {
        return Column::make($name,'integer')
        ->setDefault(0)
        ->setLimit(20)
        ->setSigned(false);
    }
}
