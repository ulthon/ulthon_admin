<?php

namespace app\common;

class TextFormat  
{
  public static function br($content){
    $content = str_replace("\n",'<br/>',$content);

    return $content;
  }
}
