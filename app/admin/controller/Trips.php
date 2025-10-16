<?php
namespace app\admin\controller;

class Trips extends AdminBase
{
    public function _config() {
        $this->modules    = 'trips';
        $this->pk         = 'trips_id';
        $this->base_where = [];
        $this->order      = 'trips_sort DESC';
        $this->top_btn    = "<button class=\"layui-btn layui-btn-sm\" onclick=\"dialog('/admin2022.php/".request()->controller()."/add.html', '添加')\">添加</button>";

        $this->search = [
            "trips_title" => 'input|标题',
        ];

        $this->column = [
            ['field'=>'trips_id','width'=>60,'title'=>'ID'],
            ['field'=>'trips_title','title'=>'文章标题'],
            ['field'=>'trips_cover','title'=>'文章封面','width'=>150],
            // ['field'=>'trips_img','title'=>'文章内容图片'],
            // ['field'=>'trips_content','title'=>'文章正文'],
            ['field'=>'trips_sort','title'=>'文章权重','width'=>100],
            ['field'=>'trips_addtime','title'=>'创建时间','width'=>160],
            ['field'=>'sys_action','align'=>'left','width'=>150,'fixed'=>'right','title'=>'操作']
        ];

        $this->add_form = [
            ['type'=>'text','title'=>'文章标题','field'=>'trips_title'],
            ['type'=>'image','title'=>'文章封面','field'=>'trips_cover'],
            // ['type'=>'image','title'=>'文章内容图片','field'=>'trips_img'],
            ['type'=>'editor','title'=>'文章正文','field'=>'trips_content'],
            ['type'=>'text','title'=>'文章权重','field'=>'trips_sort'],
        ];
        $this->tpl_dir = 'base';
    }

    public function _operate($row)
    {
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-warm layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?trips_id={$row['trips_id']}', '编辑', ['1000px', '800px'])\">编辑</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            $rows[$k]['trips_addtime'] = date('Y-m-d H:i:s', $v['trips_addtime']);
            $rows[$k]['trips_cover']   = list_img_format($v['trips_cover']);
            $rows[$k]['sys_action']    = $this->_operate($v);
        }
        return $rows;
    }
}