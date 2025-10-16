<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class IndexInfo extends AdminBase
{
    public function _config(){
        $this->modules    = 'index_info';
        $this->pk         = 'index_info_id';
        $this->base_where = [];
        $this->order      = 'index_info_sort DESC';
        $this->top_btn = "<button class=\"layui-btn layui-btn-sm\" onclick=\"dialog('/admin2022.php/".request()->controller()."/add.html', '添加')\">添加</button>";

        $this->search = [
            "index_info_title" => 'input|标题',
        ];

        $this->column = [
            ['field'=>'index_info_id','width'=>90,'title'=>'ID'],
            ['field'=>'index_info_title','title'=>'标题'],
            ['field'=>'index_info_image','title'=>'图片'],
            ['field'=>'index_info_desc','title'=>'简述'],
            ['field'=>'index_info_sort','title'=>'权重'],
            ['field'=>'sys_action','align'=>'left','width'=>150,'fixed'=>'right','title'=>'操作']
        ];

        $this->add_form = [
            ['type'=>'text','title'=>'标题','field'=>'index_info_title'],
            ['type'=>'image','title'=>'图片','field'=>'index_info_image'],
            ['type'=>'textarea','title'=>'简述','field'=>'index_info_desc'],
            ['type'=>'text','title'=>'权重','field'=>'index_info_sort'],
        ];
        $this->tpl_dir = 'base';
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            $rows[$k]['index_info_image'] = list_img_format($v['index_info_image']);
            $rows[$k]['sys_action'] = $this->_operate($v);
        }
        return $rows;
    }

    public function _operate($row)
    {
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-warm layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?index_info_id={$row['index_info_id']}', '编辑', ['1000px', '800px'])\">编辑</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }
}
?>