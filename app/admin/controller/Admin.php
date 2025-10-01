<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\Validate;

class Admin extends AdminBase
{
    public function _config(){
        $this->modules = 'admin';
        $this->pk = 'admin_id';
        $this->base_where = [];
        
        $this->top_btn = "<button class=\"layui-btn layui-btn-sm\" onclick=\"dialog('/admin2022.php/".request()->controller()."/add.html', '添加')\">添加</button>";
        $this->search = [
            "admin_name"    => 'input|姓名',
            "admin_account" => 'input|账号',
        ];
        if(input('get.other__daterange_clue_addtime')){
            $temp = explode(' - ', input('get.other__daterange_clue_addtime'));
            $stime = strtotime($temp[0].' 00:00:00');
            $etime = strtotime($temp[1].' 23:59:59');
            $this->base_where[] = ['clue_addtime', 'between', [$stime, $etime]];
        }
        $this->column = [
            ['type'=>'numbers'],
            ['field'=>'admin_name','title'=>'姓名'],
            ['field'=>'admin_account','title'=>'账号'],
            // ['field'=>'admin_invitation_code','title'=>'邀请码'],
            ['field'=>'admin_group_name','title'=>'所属角色'],
            ['field'=>'sys_action','title'=>'操作','align'=>'center','width'=>120,'fixed'=>'right']
        ];
        $this->add_form = [
            ['type'=>'radio','title'=>'权限','field'=>'admin_group_id','options'=>AdminQuick::select_options('admin_group', [], 'admin_group_id', 'admin_group_name', 'admin_group_sort asc')],
            ['type'=>'text','title'=>'姓名','field'=>'admin_name'],
            ['type'=>'text','title'=>'账号','field'=>'admin_account'],
            ['type'=>'password','title'=>'密码','field'=>'admin_pwd'],
        ];
        $this->edit_form = $this->add_form;
        $this->tpl_dir = 'base';
    }

    public function listsFormat($rows){
        foreach ($rows as $k => $v) {
            $rows[$k][$this->modules.'_addtime'] = $v[$this->modules.'_addtime'] ? date("Y-m-d H:i:s",$v[$this->modules.'_addtime']) : '';
            $rows[$k]['admin_logintime'] = $v['admin_logintime'] ? date("Y-m-d H:i:s",$v['admin_logintime']) : '';
            $rows[$k]['sys_action'] = $this->_operate($v);
            $rows[$k]['admin_group_name'] = Db::name('admin_group')->where('admin_group_id', $v['admin_group_id'])->value('admin_group_name');
        }
        return $rows;
    }

    public function _operate($row)
    {
        $controller = request()->controller();
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?admin_id={$row['admin_id']}', '编辑', ['1024px', '600px'])\">编辑</a>";
        if($row['admin_id'] > 1){
            $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        }
        return $html;
    }

    public function editPrev($data){
        $data['admin_pwd'] = '';
        return $data;
    }

    public function saveDoPrev($data){
        if(input('post.admin_id') > 0){
            // 编辑
            if(Db::name('admin')->where('admin_account','=',$data['admin_account'])->where('admin_id','<>',input('post.admin_id'))->find()){
                return ['isSuccess'=>false,'msg'=>'账号已存在'];
            }
        } else {
            // 添加
            if(Db::name('admin')->where('admin_account','=',$data['admin_account'])->find()){
                return ['isSuccess'=>false,'msg'=>'账号已存在'];
            }
        }

        if($data['admin_pwd'] == ''){
            unset($data['admin_pwd']);
        } else {
            $data['admin_pwd'] = hash('sha256', $data['admin_pwd']);
        }
        $data['admin_state'] = 1;
        return $data;
    }

    public function password(){
        if (request()->isPost()) {
            $params = request()->post();
            if (!Validate::is($params['old_password'], "require")) {
                return error('请输入原密码');
            }
            if (!Validate::is($params['new_password'], "require")) {
                return error('请输入新密码');
            }
            if (!Validate::is($params['renew_password'], "require")) {
                return error('请再次输入新密码');
            }
            if ($params['new_password'] != $params['renew_password']) {
                return error('两次密码输入不一致');
            }
            if(hash('sha256', $params['old_password']) == $this->admin['admin_pwd']){
                Db::name('admin')->where('admin_id', '=', $this->admin['admin_id'])->update(['admin_pwd'=>hash('sha256', $params['new_password'])]);
                return success('修改成功');
            } else {
                return error('原密码错误');
            }
        }
        return view();
    }
}