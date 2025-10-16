<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;

class AdminGroup extends AdminBase
{
    public function _config(){
        $this->modules = 'admin_group';
        $this->pk = 'admin_group_id';
        $this->base_where = [];
        $this->order = 'admin_group_id DESC';
        $this->top_btn = "<button class=\"layui-btn layui-btn-sm\" onclick=\"dialog('/admin2022.php/".request()->controller()."/add.html', '添加')\">添加</button>";
        $this->search = [
            "admin_group_id"   => 'input|编号',
            "admin_group_name"   => 'input|名称',
        ];
    
        $this->column = [
            ['field'=>'admin_group_id','width'=>80,'title'=>'ID'],
            ['field'=>'admin_group_name','title'=>'名称'],
            // ['field'=>'admin_group_auth','title'=>'权限'],
            ['field'=>'sys_action','align'=>'left','width'=>160,'fixed'=>'right','title'=>'操作']
        ];
        $this->add_form = [
            ['type'=>'text','title'=>'名称','field'=>'admin_group_name'],
            ['type'=>'checkbox-auth','title'=>'权限','field'=>'admin_group_auth','options'=>'User:用户管理;Category:分类管理;Product:商品管理;Order:订单管理;AdminGroup:角色管理;Admin:账号管理'],
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
            $html .= "<a class=\"layui-btn layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?admin_group_id={$row['admin_group_id']}', '编辑')\">编辑</a>";
        if ($row['admin_group_id'] != 1) {
            $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        }
        return $html;
    }

    // public function editPrev($data){
    //     $data['admin_group_auth'] = explode(',', $data['admin_group_auth']);
    //     return $data;
    // }

    public function saveDoPrev($data){
        if(isset($data['admin_group_auth'])){
            $data['admin_group_auth'] = implode(',', $data['admin_group_auth']);
        } else {
            $data['admin_group_auth'] = '';
        }
        
        return $data;
    }
}