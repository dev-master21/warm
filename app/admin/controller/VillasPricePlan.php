<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class VillasPricePlan extends AdminBase
{
    public function _config(){
        $this->modules    = 'villas_price_plan';
        $this->pk         = 'villas_price_plan_id';
        $this->base_where = [];
        $this->order      = 'villas_price_plan_sort DESC';
        $this->top_btn    = "<button class=\"layui-btn layui-btn-sm\" onclick=\"dialog('/admin2022.php/".request()->controller()."/add.html?addauto__villas_id=".input('get.wheremap__villas_id')."', '添加')\">添加</button>";

        $this->search = [];

        $this->column = [
            ['field'=>'villas_price_plan_id','width'=>90,'title'=>'ID'],
            ['field'=>'villas_price_plan_stime','title'=>'开始时间'],
            ['field'=>'villas_price_plan_etime','title'=>'结束时间'],
            ['field'=>'villas_price_plan_original','title'=>'原价'],
            ['field'=>'villas_price_plan_now','title'=>'现价'],
            ['field'=>'sys_action','align'=>'left','width'=>150,'fixed'=>'right','title'=>'操作']
        ];

        $this->add_form = [
            ['type'=>'dateymd','title'=>'开始时间','field'=>'villas_price_plan_stime'],
            ['type'=>'dateymd','title'=>'结束时间','field'=>'villas_price_plan_etime'],
            ['type'=>'text','title'=>'原价','field'=>'villas_price_plan_original'],
            ['type'=>'text','title'=>'现价','field'=>'villas_price_plan_now'],
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
        $html .= "<a class=\"layui-btn layui-btn-warm layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?villas_price_plan_id={$row['villas_price_plan_id']}', '编辑')\">编辑</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }
}
?>