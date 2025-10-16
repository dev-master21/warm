<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class Banner extends AdminBase
{
    public function _config(){
        $this->modules    = 'banner';
        $this->pk         = 'banner_id';
        $this->base_where = [];
        $this->order      = 'banner_sort DESC';
        $this->top_btn    = "<button class=\"layui-btn layui-btn-sm\" onclick=\"dialog('/admin2022.php/".request()->controller()."/add.html', '添加')\">添加</button>";
        $this->search = [
            
        ];
        $this->column = [
            ['field'=>'banner_id','align'=>'center','width'=>60,'title'=>'ID'],
            ['field'=>'banner_sort','width'=>80,'title'=>'权重'],
            ['field'=>'banner_name','title'=>'名称'],
            ['field'=>'banner_picture','width'=>180,'title'=>'图片'],
            // ['field'=>'banner_location','width'=>180,'title'=>'位置'],
            ['field'=>'banner_addtime','width'=>160,'title'=>'添加时间'],
            ['field'=>'sys_action','align'=>'left','width'=>200,'fixed'=>'right']
        ];
        $this->add_form = [
            ['type'=>'text','title'=>'名称','field'=>'banner_name'],
            ['type'=>'image','title'=>'图片','field'=>'banner_picture'],
            ['type'=>'text','title'=>'权重','field'=>'banner_sort'],
            // ['type'=>'radio','title'=>'位置','field'=>'banner_location','options'=>'房产首页:房产首页;房产详情:房产详情'],
        ];
        $this->tpl_dir = 'base';
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            $rows[$k]['banner_addtime'] = date('Y-m-d H:i:s',$v['banner_addtime']);
            $rows[$k]['banner_picture'] = list_img_format($v['banner_picture']);
            $rows[$k]['sys_action']     = $this->_operate($v);
        }
        return $rows;
    }

    public function _operate($row)
    {
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?banner_id={$row['banner_id']}', '编辑')\">编辑</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }
}