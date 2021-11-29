<?php

namespace app\common\tools;

use EasyAdmin\curd\BuildCurd as CurdBuildCurd;

class BuildCurd extends CurdBuildCurd
{



    /**
     * 表单类型
     * @var array
     */
    protected $formTypeArray = ['text', 'image', 'images', 'file', 'files', 'select', 'switch', 'date', 'editor', 'textarea', 'checkbox', 'radio', 'relation'];

    public function getTableColumns()
    {
        return $this->tableColumns;
    }


    /**
     * 构建初始化字段信息
     * @param $colum
     * @return mixed
     */
    protected function buildColum(&$colum)
    {

        $string = $colum['comment'];

        // 处理定义类型
        preg_match('/{[\s\S]*?}/i', $string, $formTypeMatch);
        if (!empty($formTypeMatch) && isset($formTypeMatch[0])) {
            $colum['comment'] = str_replace($formTypeMatch[0], '', $colum['comment']);
            $formType = trim(str_replace('}', '', str_replace('{', '', $formTypeMatch[0])));
            if (in_array($formType, $this->formTypeArray)) {
                $colum['formType'] = $formType;
            }
        }

        // 处理默认定义
        preg_match('/\([\s\S]*?\)/i', $string, $defineMatch);
        if (!empty($formTypeMatch) && isset($defineMatch[0])) {
            $colum['comment'] = str_replace($defineMatch[0], '', $colum['comment']);

            if (isset($colum['formType']) && in_array($colum['formType'], ['images', 'files', 'select', 'switch', 'radio', 'checkbox', 'date', 'relation'])) {
                $define = str_replace(')', '', str_replace('(', '', $defineMatch[0]));

                if (in_array($colum['formType'], ['select', 'switch', 'radio', 'checkbox', 'relation'])) {
                    $formatDefine = [];
                    $explodeArray = explode(',', $define);
                    foreach ($explodeArray as $vo) {
                        $voExplodeArray = explode(':', $vo);
                        if (count($voExplodeArray) == 2) {
                            $formatDefine[trim($voExplodeArray[0])] = trim($voExplodeArray[1]);
                        }
                    }
                    !empty($formatDefine) && $colum['define'] = $formatDefine;
                } else {
                    $colum['define'] = $define;
                }
            }
        }

        $colum['comment'] = trim($colum['comment']);

        return $colum;
    }
}
