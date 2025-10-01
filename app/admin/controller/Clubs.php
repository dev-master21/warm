<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class Clubs extends AdminBase
{
    public function _config(){
        $this->modules    = 'clubs';
        $this->pk         = 'clubs_id';
        $this->base_where = [];
        $this->order      = 'clubs_id DESC';
        $this->top_btn    = "";

        $this->search = [
            "clubs_email" => 'input|邮箱',
        ];

        $this->column = [
            ['title'=>'#', 'field'=>'clubs_id','width'=>60],
            ['title'=>'邮箱', 'field'=>'clubs_email'],
            ['title'=>'添加时间', 'field'=>'clubs_addtime','width'=>180],
            ['field'=>'sys_action','align'=>'left','width'=>120,'fixed'=>'right','title'=>'操作']
        ];

        $this->add_form = [];
        $this->tpl_dir = 'base';
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            $rows[$k]['clubs_addtime'] = date('Y-m-d H:i:s', $v['clubs_addtime']);
            $rows[$k]['sys_action'] = $this->_operate($v);
        }
        return $rows;
    }

    public function _operate($row)
    {
        $html  = '';
        // $html .= "<a class=\"layui-btn layui-btn-warm layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?clubs_id={$row['clubs_id']}', '查看', ['1000px', '800px'])\">查看</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }
}
?>