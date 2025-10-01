<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class Testimonials extends AdminBase
{
    public function _config(){
        $this->modules    = 'testimonials';
        $this->pk         = 'testimonials_id';
        $this->base_where = [];
        $this->order      = 'testimonials_sort DESC';
        $this->top_btn = "<button class=\"layui-btn layui-btn-sm\" onclick=\"dialog('/admin2022.php/".request()->controller()."/add.html', '添加')\">添加</button>";

        $this->search = [
            "testimonials_name" => 'input|姓名',
        ];

        $this->column = [
            ['field'=>'testimonials_id','width'=>90,'title'=>'ID'],
            ['field'=>'testimonials_name','title'=>'姓名'],
            ['field'=>'testimonials_desc','title'=>'评价'],
            ['field'=>'testimonials_sort','title'=>'权重'],
            ['field'=>'sys_action','align'=>'left','width'=>150,'fixed'=>'right','title'=>'操作']
        ];

        $this->add_form = [
            ['type'=>'text','title'=>'姓名','field'=>'testimonials_name'],
            ['type'=>'textarea','title'=>'评价','field'=>'testimonials_desc'],
            ['type'=>'text','title'=>'权重','field'=>'testimonials_sort'],
        ];
        $this->tpl_dir = 'base';
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            $rows[$k]['sys_action'] = $this->_operate($v);
        }
        return $rows;
    }

    public function _operate($row)
    {
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-warm layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?testimonials_id={$row['testimonials_id']}', '编辑', ['1000px', '800px'])\">编辑</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }
}
?>