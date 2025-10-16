<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class Chip extends AdminBase
{
    public function _config(){
        $this->modules    = 'chip';
        $this->pk         = 'chip_id';
        $this->base_where = [];
        $this->order      = 'chip_id DESC';
        $this->top_btn    = "";

        $this->search = [];

        $this->column = [
            ['title'=>'ID',   'field'=>'chip_id',    'width'=>60],
            ['title'=>'图片', 'field'=>'chip_image', 'width'=>80],
            ['title'=>'标题', 'field'=>'chip_title'],
            ['title'=>'简述', 'field'=>'chip_desc'],
            ['field'=>'sys_action','align'=>'left','width'=>120,'fixed'=>'right','title'=>'操作']
        ];

        $this->add_form = [
            ['type'=>'image',    'title'=>'图片', 'field'=>'chip_image'],
            ['type'=>'text',     'title'=>'标题', 'field'=>'chip_title'],
            ['type'=>'textarea', 'title'=>'简述', 'field'=>'chip_desc'],
        ];
        $this->tpl_dir = 'base';
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            $rows[$k]['chip_image']   = list_img_format($v['chip_image']);
            $rows[$k]['chip_addtime'] = date('Y-m-d H:i:s', $v['chip_addtime']);
            $rows[$k]['sys_action']   = $this->_operate($v);
        }
        return $rows;
    }

    public function _operate($row)
    {
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-normal layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?chip_id={$row['chip_id']}', '编辑', ['1000px', '800px'])\">编辑</a>";
        // $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }
}
?>