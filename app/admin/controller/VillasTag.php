<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class VillasTag extends AdminBase
{
    public function _config(){
        $this->modules    = 'villas_tag';
        $this->pk         = 'villas_tag_id';
        $this->base_where = [];
        $this->order      = 'villas_tag_sort DESC';
        $this->top_btn    = "";

        $this->search = [];

        $this->column = [
            ['field'=>'villas_tag_id','width'=>90,'title'=>'ID'],
            // ['field'=>'villas_tag_sort','width'=>90,'title'=>'权重'],
            ['field'=>'villas_tag_image','width'=>120,'title'=>'封面'],
            ['field'=>'villas_tag_banner','width'=>120,'title'=>'Banner'],
            ['field'=>'villas_tag_name','width'=>160,'title'=>'名称'],
            ['field'=>'villas_tag_desc','title'=>'简介'],
            ['field'=>'villas_tag_detail','title'=>'详情'],
            ['field'=>'sys_action','align'=>'left','width'=>80,'fixed'=>'right','title'=>'操作']
        ];

        $this->add_form = [
            ['type'=>'image','field'=>'villas_tag_image','title'=>'封面'],
            ['type'=>'image','field'=>'villas_tag_banner','title'=>'Banner'],
            ['type'=>'text','field'=>'villas_tag_name','title'=>'标题'],
            ['type'=>'textarea','field'=>'villas_tag_desc','title'=>'简介'],
            ['type'=>'textarea','field'=>'villas_tag_detail','title'=>'详情'],
        ];
        $this->tpl_dir = 'base';
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            $rows[$k]['villas_tag_image']  = list_img_format($v['villas_tag_image']);
            $rows[$k]['villas_tag_banner'] = list_img_format($v['villas_tag_banner']);
            $rows[$k]['sys_action'] = $this->_operate($v);
        }
        return $rows;
    }

    public function _operate($row)
    {
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-warm layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?villas_tag_id={$row['villas_tag_id']}', '编辑')\">编辑</a>";
        // $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }
}
?>