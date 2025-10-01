<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class Contact extends AdminBase
{
    public function _config(){
        $this->modules    = 'contact';
        $this->pk         = 'contact_id';
        $this->base_where = [];
        $this->order      = 'contact_id DESC';
        $this->top_btn    = "";

        $this->search = [
            "contact_email" => 'input|Email',
            "contact_country" => 'input|Country',
        ];

        $this->column = [
            ['title'=>'#', 'field'=>'contact_id','width'=>60],
            ['title'=>'Email', 'field'=>'contact_email','width'=>200],
            ['title'=>'Country', 'field'=>'contact_country','width'=>100],
            ['title'=>'Travel Dates From', 'field'=>'contact_travel_dates_from','width'=>140],
            ['title'=>'Travel Dates To', 'field'=>'contact_travel_dates_to','width'=>140],
            ['title'=>'Message', 'field'=>'contact_message'],
            ['title'=>'Addtime', 'field'=>'contact_addtime','width'=>180],
            ['field'=>'sys_action','align'=>'left','width'=>80,'fixed'=>'right','title'=>'操作']
        ];

        $this->add_form = [];
        $this->tpl_dir = 'base';
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            $rows[$k]['contact_addtime'] = date('Y-m-d H:i:s', $v['contact_addtime']);
            $rows[$k]['sys_action'] = $this->_operate($v);
        }
        return $rows;
    }

    public function _operate($row)
    {
        $html  = '';
        // $html .= "<a class=\"layui-btn layui-btn-warm layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?contact_id={$row['contact_id']}', '查看', ['1000px', '800px'])\">查看</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }
}
?>