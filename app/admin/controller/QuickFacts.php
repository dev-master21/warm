<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class QuickFacts extends AdminBase
{
    public function _config(){
        $this->modules    = 'quick_facts';
        $this->pk         = 'quick_facts_id';
        $this->base_where = [];
        $this->order      = 'quick_facts_sort DESC';
        $this->top_btn    = "<button class=\"layui-btn layui-btn-sm\" onclick=\"dialog('/admin2022.php/".request()->controller()."/add.html?addauto__villas_id=".input('get.wheremap__villas_id')."', '添加')\">添加</button>";

        $this->search = [
            "quick_facts_title" => 'input|标题',
        ];

        $this->column = [
            ['field'=>'quick_facts_id','width'=>90,'title'=>'ID'],
            ['field'=>'quick_facts_sort','width'=>90,'title'=>'权重'],
            ['field'=>'quick_facts_name','title'=>'标题'],
            ['field'=>'quick_facts_desc','title'=>'内容'],
            ['field'=>'sys_action','align'=>'left','width'=>150,'fixed'=>'right','title'=>'操作']
        ];

        $this->add_form = [
            ['type'=>'text','title'=>'标题','field'=>'quick_facts_name'],
            ['type'=>'textarea','title'=>'内容','field'=>'quick_facts_desc'],
            ['type'=>'text','title'=>'权重','field'=>'quick_facts_sort'],
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
        $html .= "<a class=\"layui-btn layui-btn-warm layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?quick_facts_id={$row['quick_facts_id']}', '编辑')\">编辑</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }
}
?>