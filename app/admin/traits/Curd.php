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

namespace app\admin\traits;

use app\common\tools\PathTools;
use EasyAdmin\annotation\NodeAnotation;


/**
 * 后台CURD复用
 * Trait Curd
 * @package app\admin\traits
 */
trait Curd
{


    /**
     * @NodeAnotation(title="列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            if (input('selectFields')) {
                return $this->selectList();
            }
            list($page, $limit, $where) = $this->buildTableParames();
            $count = $this->model
                ->where($where)
                ->count();
            $list = $this->model
                ->where($where)
                ->page($page, $limit)
                ->order($this->sort)
                ->select();
            $data = [
                'code'  => 0,
                'msg'   => '',
                'count' => $count,
                'data'  => $list,
            ];
            return json($data);
        }
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="添加")
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);
            try {
                $save = $this->model->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败:' . $e->getMessage());
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="编辑")
     */
    public function edit($id)
    {
        $row = $this->model->find($id);
        empty($row) && $this->error('数据不存在');
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);
            try {
                $save = $row->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败');
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        $this->assign('row', $row);
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="删除")
     */
    public function delete($id)
    {
        $this->checkPostRequest();
        $row = $this->model->whereIn('id', $id)->select();
        $row->isEmpty() && $this->error('数据不存在');
        try {
            $save = $row->delete();
        } catch (\Exception $e) {
            $this->error('删除失败');
        }
        $save ? $this->success('删除成功') : $this->error('删除失败');
    }

    /**
     * @NodeAnotation(title="导出")
     */
    public function export()
    {
        list($page, $limit, $where) = $this->buildTableParames();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $fields = $this->request->param('fields', '{}', null);
        $image_fields = $this->request->param('image_fields', '{}', null);
        $select_fields = $this->request->param('select_fields', '{}', null);

        $fields = json_decode($fields, true);
        $image_fields = json_decode($image_fields, true);
        $select_fields = json_decode($select_fields, true);

        $write_col = 1;
        $write_line = 1;
        foreach ($fields as $field_key => $field_name) {
            $col_key = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($write_col);
            $sheet->setCellValue($col_key . $write_line, $field_name);

            $write_col++;
        }

        $runtime_file_list = [];

        $this->model
            ->where($where)->chunk(100, function ($list) use ($sheet, &$write_line, $fields, $image_fields, &$runtime_file_list, $select_fields) {
                foreach ($list as $list_index => $item) {
                    $write_line++;
                    $write_col = 1;

                    foreach ($fields as $field_key => $field_name) {
                        $col_key = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($write_col);

                        $cel =  null;

                        $value  = \think\helper\Arr::get($item, $field_key);

                        if (in_array($field_key, $image_fields)) {
                            // 是图片
                            $cel = $value;

                            try {

                                if (filter_var($value, FILTER_VALIDATE_URL)) {

                                    $runtime_file = PathTools::tempBuildPath(uniqid());

                                    $runtime_file_list[] = $runtime_file;

                                    file_put_contents($runtime_file, file_get_contents($value));

                                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                    $drawing->setName($field_name);
                                    $drawing->setDescription($field_key);
                                    $drawing->setPath($runtime_file);
                                    $drawing->setHeight(36);

                                    $drawing->setCoordinates($col_key . $write_line);
                                    $drawing->setWorksheet($sheet);
                                }
                            } catch (\Throwable $th) {
                                $message = $th->getMessage();

                                $cel .= "\n" . $message;
                            }
                        } else if (array_key_exists($field_key, $select_fields)) {
                            // 需要设置选项

                            $cel = $select_fields[$field_key][$value];
                        } else {
                            $cel  = $value;
                        }

                        $sheet->setCellValue($col_key . $write_line, $cel);
                        $write_col++;
                    }
                }
            });

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_contents();
        ob_clean();

        foreach ($runtime_file_list as  $runtime_file) {
            unlink($runtime_file);
        }

        return download($content, $this->model->getName() . date('Ymd') . '.xlsx', true);
    }

    /**
     * @NodeAnotation(title="属性修改")
     */
    public function modify()
    {
        $this->checkPostRequest();
        $post = $this->request->post();
        $rule = [
            'id|ID'    => 'require',
            'field|字段' => 'require',
            'value|值'  => 'require',
        ];
        $this->validate($post, $rule);
        $row = $this->model->find($post['id']);
        if (!$row) {
            $this->error('数据不存在');
        }
        if (!in_array($post['field'], $this->allowModifyFields)) {
            $this->error('该字段不允许修改：' . $post['field']);
        }
        try {
            $row->save([
                $post['field'] => $post['value'],
            ]);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('保存成功');
    }
}
