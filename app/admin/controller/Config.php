<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class Config extends AdminBase
{
    public function _config(){
        $this->modules    = 'config';
        $this->pk         = 'config_id';
        $this->base_where = [];
        $this->top_btn    = "";
        $this->order      = 'config_type DESC,config_id ASC';
        $this->search = [
            "config_title" => 'input|配置项',
        ];
        $this->column = [
            ['field'=>'config_id','align'=>'center','width'=>60,'title'=>'ID'],
            ['field'=>'config_type','width'=>200,'title'=>'类别'],
            ['field'=>'config_title','width'=>260,'title'=>'配置项'],
            ['field'=>'config_value','title'=>'配置值'],
            ['field'=>'sys_action','align'=>'left','width'=>80,'fixed'=>'right']
        ];
        $this->tpl_dir = 'base';
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            if ($v['config_field_type'] == 'image')
            {
                $rows[$k]['config_value'] = list_img_format($v['config_value']);
            } else if($v['config_field_type'] == 'file') {
                $rows[$k]['config_value'] = '<a href="/storage/'.$v['config_value'].'" target="_blank" style="color:#009688">下载</a>';
            } else {
                $rows[$k]['config_value'] = strip_tags($v['config_value']);
            }
            $rows[$k]['sys_action']   = $this->_operate($v);
        }
        return $rows;
    }

    public function _operate($row)
    {
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?config_id={$row['config_id']}', '编辑', ['800px', '600px'])\">编辑</a>";
        return $html;
    }
    
    public function edit()
    {
        if(request()->isAjax()){
            $data = request()->post();
            $resp = Db::name($this->modules)->save($data);
            return success('提交成功');
        }

        $data = Db::name($this->modules)->find(request()->get($this->pk));
        View::assign('data', $data);
        
        $form_html  = '';
        $form_html .= View::fetch("form/".$data['config_field_type'],[
            'title'   => $data['config_title'],
            'field'   => 'config_value',
            'value'   => $data['config_value'],
        ]);
        View::assign('form_html', $form_html);
        return view("{$this->tpl_dir}/dialog_form");
    }
}