<?php
namespace app\common;

use think\migration\db\Column;

class ColumnFormat  
{
    public static function timestamp($name){
        return Column::make($name,'integer')
        ->setLimit(10)
        ->setSigned(false);
    }
    public static function loadDeleteTime(){
        return Column::make('delete_time','integer')
        ->setLimit(10)
        ->setSigned(false);
    }
}
