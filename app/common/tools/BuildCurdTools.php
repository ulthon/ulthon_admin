<?php

namespace app\common\tools;

use EasyAdmin\curd\BuildCurd;
use EasyAdmin\tool\CommonTool;

class BuildCurdTools extends BuildCurd
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

    /**
     * 初始化视图
     * @return $this
     */
    protected function renderView()
    {
        // 列表页面
        $viewIndexFile = "{$this->rootDir}app{$this->DS}admin{$this->DS}view{$this->DS}{$this->viewFilename}{$this->DS}index.html";
        $viewIndexValue = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}index"),
            [
                'controllerUrl' => $this->controllerUrl,
            ]
        );
        $this->fileList[$viewIndexFile] = $viewIndexValue;

        // 添加页面
        $viewAddFile = "{$this->rootDir}app{$this->DS}admin{$this->DS}view{$this->DS}{$this->viewFilename}{$this->DS}add.html";
        $addFormList = '';
        foreach ($this->tableColumns as $field => $val) {

            if (in_array($field, ['id', 'create_time'])) {
                continue;
            }

            $templateFile = "view{$this->DS}module{$this->DS}input";
            $define = '';

            // 根据formType去获取具体模板
            if ($val['formType'] == 'image') {
                $templateFile = "view{$this->DS}module{$this->DS}image";
            } elseif ($val['formType'] == 'images') {
                $templateFile = "view{$this->DS}module{$this->DS}images";
                $define = isset($val['define']) ? $val['define'] : '|';
            } elseif ($val['formType'] == 'file') {
                $templateFile = "view{$this->DS}module{$this->DS}file";
            } elseif ($val['formType'] == 'files') {
                $templateFile = "view{$this->DS}module{$this->DS}files";
                $define = isset($val['define']) ? $val['define'] : '|';
            } elseif ($val['formType'] == 'editor') {
                $templateFile = "view{$this->DS}module{$this->DS}editor";
            } elseif ($val['formType'] == 'date') {
                $templateFile = "view{$this->DS}module{$this->DS}date";
                if (isset($val['define']) && !empty($val['define'])) {
                    $define = $val['define'];
                } else {
                    $define = 'datetime';
                }
                if (!in_array($define, ['year', 'month', 'date', 'time', 'datetime'])) {
                    $define = 'datetime';
                }
            } elseif ($val['formType'] == 'radio') {
                $templateFile = "view{$this->DS}module{$this->DS}radio";
                if (isset($val['define']) && !empty($val['define'])) {
                    $define = $this->buildRadioView($field, '{in name="k" value="' . $val['default'] . '"}checked=""{/in}');
                }
            } elseif ($val['formType'] == 'checkbox') {
                $templateFile = "view{$this->DS}module{$this->DS}checkbox";
                if (isset($val['define']) && !empty($val['define'])) {
                    $define = $this->buildCheckboxView($field, '{in name="k" value="' . $val['default'] . '"}checked=""{/in}');
                }
            } elseif ($val['formType'] == 'select') {
                $templateFile = "view{$this->DS}module{$this->DS}select";
                if (isset($val['bindRelation'])) {
                    $define = $this->buildOptionView($val['bindRelation']);
                } elseif (isset($val['define']) && !empty($val['define'])) {
                    $define = $this->buildOptionView($field);
                }
            } elseif (in_array($field, ['remark']) || $val['formType'] == 'textarea') {
                $templateFile = "view{$this->DS}module{$this->DS}textarea";
            } elseif ($val['formType'] == 'relation') {
                // 使用select生成
                $templateFile = "view{$this->DS}module{$this->DS}select";
                if (isset($val['bindRelation'])) {
                    $define = $this->buildOptionView($val['bindRelation']);
                } elseif (isset($val['define']) && !empty($val['define'])) {
                    $define = $this->buildOptionView($field);
                }
            }

            $addFormList .= CommonTool::replaceTemplate(
                $this->getTemplate($templateFile),
                [
                    'comment'  => $val['comment'],
                    'field'    => $field,
                    'required' => $this->buildRequiredHtml($val['required']),
                    'value'    => $val['default'],
                    'define'   => $define,
                ]
            );
        }
        $viewAddValue = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}form"),
            [
                'formList' => $addFormList,
            ]
        );
        $this->fileList[$viewAddFile] = $viewAddValue;


        // 编辑页面
        $viewEditFile = "{$this->rootDir}app{$this->DS}admin{$this->DS}view{$this->DS}{$this->viewFilename}{$this->DS}edit.html";
        $editFormList = '';
        foreach ($this->tableColumns as $field => $val) {

            if (in_array($field, ['id', 'create_time'])) {
                continue;
            }

            $templateFile = "view{$this->DS}module{$this->DS}input";

            $define = '';
            $value = '{$row.' . $field . '|default=\'\'}';

            // 根据formType去获取具体模板
            if ($val['formType'] == 'image') {
                $templateFile = "view{$this->DS}module{$this->DS}image";
            } elseif ($val['formType'] == 'images') {
                $templateFile = "view{$this->DS}module{$this->DS}images";
            } elseif ($val['formType'] == 'file') {
                $templateFile = "view{$this->DS}module{$this->DS}file";
            } elseif ($val['formType'] == 'files') {
                $templateFile = "view{$this->DS}module{$this->DS}files";
            } elseif ($val['formType'] == 'editor') {
                $templateFile = "view{$this->DS}module{$this->DS}editor";
                $value = '{$row.' . $field . '|raw|default=\'\'}';
            } elseif ($val['formType'] == 'date') {
                $templateFile = "view{$this->DS}module{$this->DS}date";
                if (isset($val['define']) && !empty($val['define'])) {
                    $define = $val['define'];
                } else {
                    $define = 'datetime';
                }
                if (!in_array($define, ['year', 'month', 'date', 'time', 'datetime'])) {
                    $define = 'datetime';
                }
            } elseif ($val['formType'] == 'radio') {
                $templateFile = "view{$this->DS}module{$this->DS}radio";
                if (isset($val['define']) && !empty($val['define'])) {
                    $define = $this->buildRadioView($field, '{in name="k" value="$row.' . $field . '"}checked=""{/in}');
                }
            } elseif ($val['formType'] == 'checkbox') {
                $templateFile = "view{$this->DS}module{$this->DS}checkbox";
                if (isset($val['define']) && !empty($val['define'])) {
                    $define = $this->buildCheckboxView($field, '{in name="k" value="$row.' . $field . '"}checked=""{/in}');
                }
            } elseif ($val['formType'] == 'select') {
                $templateFile = "view{$this->DS}module{$this->DS}select";
                if (isset($val['bindRelation'])) {
                    $define = $this->buildOptionView($val['bindRelation'], '{in name="k" value="$row.' . $field . '"}selected=""{/in}');
                } elseif (isset($val['define']) && !empty($val['define'])) {
                    $define = $this->buildOptionView($field, '{in name="k" value="$row.' . $field . '"}selected=""{/in}');
                }
            } elseif (in_array($field, ['remark']) || $val['formType'] == 'textarea') {
                $templateFile = "view{$this->DS}module{$this->DS}textarea";
                $value = '{$row.' . $field . '|raw|default=\'\'}';
            } elseif ($val['formType'] == 'relation') {
                // 使用select生成
                $templateFile = "view{$this->DS}module{$this->DS}select";
                if (isset($val['bindRelation'])) {
                    $define = $this->buildOptionView($val['bindRelation']);
                } elseif (isset($val['define']) && !empty($val['define'])) {
                    $define = $this->buildOptionView($field);
                }
            }


            $editFormList .= CommonTool::replaceTemplate(
                $this->getTemplate($templateFile),
                [
                    'comment'  => $val['comment'],
                    'field'    => $field,
                    'required' => $this->buildRequiredHtml($val['required']),
                    'value'    => $value,
                    'define'   => $define,
                ]
            );
        }
        $viewEditValue = CommonTool::replaceTemplate(
            $this->getTemplate("view{$this->DS}form"),
            [
                'formList' => $editFormList,
            ]
        );
        $this->fileList[$viewEditFile] = $viewEditValue;

        return $this;
    }
}
