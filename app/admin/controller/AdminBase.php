<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class AdminBase
{
    public $modules;
    public $pk;
    public $base_where;
    public $order;
    public $top_btn;
    public $search;
    public $column;
    public $add_form;
    public $tpl_dir;
    public $join;
    public $admin;
    public $admin_group;
    public function __construct()
    {
        if(Cookie::get('admin_hash') == '' || Cookie::get('admin_hash') != hash('sha256', Cookie::get('admin_id').request()->ip())){
            Cookie::delete('admin_id');
            Cookie::delete('admin_hash');
            exit('<script> window.location.href = "/admin2022.php/index/login.html" </script>');
        }
        $this->admin = Db::name('admin')->find(Cookie::get('admin_id'));
        View::assign('admin', $this->admin);
        $this->_config();
        
        View::assign('modules', $this->modules);
        View::assign('pk', $this->pk);

        $admin_group = Db::name('admin_group')->find($this->admin['admin_group_id']);
        $admin_group['admin_group_auth'] = explode(',', $admin_group['admin_group_auth']);
        $this->admin_group = $admin_group;
        View::assign('admin_group', $admin_group);

        // Db::name('admin_log')->insert([
        //     'admin_log_username' => $this->admin['admin_account'],
        //     'admin_log_link'     => request()->server('REQUEST_URI'),
        //     'admin_log_addtime'  => time(),
        //     'admin_log_sort'     => 0
        // ]);
    }
    
    public function _config(){
        
    }
    
    public function lists()
    {
        if(request()->isPost()){
            $cache = [];
            foreach(request()->post() as $k => $v){
                if($v != ''){
                    $cache[$k] = $v;
                }
            }
            return redirect(url('/'.request()->controller().'/lists', $cache));
        }

        // 添加时自动添加的字段 addauto__
        $addauto = [];
        foreach(request()->get() as $k => $v){
            if(strpos($k,'addauto__') !== false && $v != ''){
                $addauto[$k] = $v;
            }
        }
        View::assign('addauto', $addauto);

        // 数据直接传递到listsDo
        View::assign('wheremap', json_encode(input('get.')));

        // 搜索处理
        View::assign('search_html', $this->_search($this->search));

        // 表格列
        View::assign('column', json_encode($this->column));

        // 添加按钮
        View::assign('top_btn', $this->top_btn);

        return view("{$this->tpl_dir}/lists");
    }

    public function category()
    {
        // 数据直接传递到listsDo
        View::assign('wheremap', json_encode(input('get.')));

        // 搜索处理
        View::assign('search_html', $this->_search($this->search));

        // 表格列
        View::assign('column', json_encode($this->column));

        // 添加按钮
        View::assign('top_btn', $this->top_btn);
        
        return view("{$this->tpl_dir}/category");
    }

    public function _search($searchField)
    {
        $html = '';
        foreach($searchField as $key => $vo){
            $c1 = explode('|', $vo);
            if($c1[0] == 'input'){
                $html .= View::fetch('search/text',['field'=>$key, 'title'=>$c1[1], 'value'=>input("get.searchmap__{$key}")]);
            }
            if($c1[0] == 'select'){
                $options = [];
                $c2 = explode(';', $c1[2]);
                foreach($c2 as $v2){
                    $c3 = explode(':', $v2);
                    $options[] = ['name'=>$c3[1],'value'=>$c3[0]];
                }
                $html .= View::fetch('search/select',['field'=>$key, 'title'=>$c1[1], 'value'=>input("get.wheremap__{$key}"), 'options'=>$options]);
            }
            if($c1[0] == 'daterange'){
                $html .= View::fetch('search/daterange',['field'=>$key, 'title'=>$c1[1], 'value'=>input("get.other__daterange_{$key}")]);
            }
            if($c1[0] == 'region'){
                $value['province_id'] = input("wheremap__{$key}_province_id");
                $value['city_id'] = input("get.wheremap__{$key}_city_id");
                $html .= View::fetch('search/region',['prefix'=>$key, 'title'=>$c1[1], 'value'=>$value]);
            }
        }
        return $html;
    }

    public function _operate($row)
    {
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?".$this->pk."={$row[$this->pk]}', 'Edit')\">Edit</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">Delete</a>";
        return $html;
    }

    public function switch_set_value()
    {
        $map["{$this->modules}_id"] = request()->post('pk');
        $data[request()->post('field')] = request()->post('value') == 'true' ? 1 : 0;
        Db::name($this->modules)->where($map)->update($data);
        return success('操作成功');
    }

    public function listsDo()
    {
        // 查询时自动添加的字段 wheremap__
        $wheremap = $this->base_where;
        foreach(request()->get() as $k => $v){
            if(strpos($k,'wheremap__') !== false && $v != ''){
                // $wheremap[] = [str_replace('__','.',str_replace('wheremap__','',$k)), '=', $v];
                if ($k != 'wheremap__device_code_status_str') {
                    $wheremap[] = [str_replace('__','.',str_replace('wheremap__','',$k)), '=', $v];
                }
            }
        }
        foreach(request()->get() as $k => $v){
            if(strpos($k,'searchmap__') !== false && $v != ''){
                $wheremap[] = [str_replace('__','.',str_replace('searchmap__','',$k)), 'like', "%{$v}%"];
            }
        }

        $model = Db::name($this->modules);
        if(isset($_GET['page']) && isset($_GET['limit'])){
            $model->page($_GET['page'], $_GET['limit']);
        }
        if(isset($this->order)){
            $model->order($this->order);
        } else {
            $model->order("{$this->modules}_sort DESC,{$this->modules}_id DESC");
        }
        
        if(isset($this->join)){
            foreach($this->join as $v){
                $model->alias($this->modules);
                $model->join($v[0], $v[1], $v[2]);
            }
        }
        $rows = $model->where($wheremap)->select()->toArray();

        $rows = $this->listsFormat($rows);
        $resp['data'] = $rows;

        $resp['count'] = $model->where($wheremap)->count();
        $resp['code']  = 0;
        return json($resp);
    }

    public function listsFormat($rows)
    {
        foreach($rows as $k => &$v) {
            $v[$this->modules.'_addtime'] = date("Y-m-d H:i:s",$v[$this->modules.'_addtime']);
            $rows[$k]['sys_action'] = $this->_operate($v);
        }
        return $rows;
    }

    public function add()
    {
        if(request()->isAjax()){
            $data = request()->post();
            $data = $this->saveDoPrev($data);
            if(isset($data['isSuccess']) && $data['isSuccess'] === false){
                return error($data['msg']);
            }
            $data[$this->modules.'_addtime'] = time();
            $resp = Db::name($this->modules)->insert($data);
            return success('提交成功');
        }

        $addauto = [];
        foreach(request()->get() as $k => $v){
            if(strpos($k,'addauto__') !== false){
                $addauto[str_replace('addauto__', '', $k)] = $v;
            }
        }
        View::assign('addauto', $addauto);

        $this->addPrev();

        $form_html  = '';
        foreach($this->add_form as $v){
            $form_html .= View::fetch("form/{$v['type']}",[
                'title'       => $v['title'],
                'field'       => $v['field'],
                'tip'         => isset($v['tip']) ? $v['tip'] : '',
                'placeholder' => isset($v['placeholder']) ? $v['placeholder'] : '',
                'value'       => isset($v['value']) ? $v['value'] : '',
                'options'     => isset($v['options']) ? $v['options'] : '',
                'require'     => isset($v['require']) ? true : false
            ]);
        }
        View::assign('form_html', $form_html);
        return view("{$this->tpl_dir}/dialog_form");
    }

    public function addPrev(){

    }

    public function edit()
    {
        if(request()->isAjax()){
            $data = request()->post();
            $data = $this->saveDoPrev($data);
            if(isset($data['isSuccess']) && $data['isSuccess'] === false){
                return error($data['msg']);
            }
            $data[$this->modules.'_addtime'] = time();
            $resp = Db::name($this->modules)->save($data);
            return success('提交成功');
        }

        $data = Db::name($this->modules)->find(request()->get($this->pk));
        View::assign('data', $data);
        View::assign('addauto', []);

        $data = $this->editPrev($data);

        $form_html  = '';
        foreach($this->add_form as $v){
            $form_html .= View::fetch("form/{$v['type']}",[
                'title'       => $v['title'],
                'field'       => $v['field'],
                'tip'         => isset($v['tip']) ? $v['tip'] : '',
                'placeholder' => isset($v['placeholder']) ? $v['placeholder'] : '',
                // 'value'       => isset($data[$v['field']]) ? $data[$v['field']] : $data,
                'value'       => $v['field'] != 'clue_region' ? $data[$v['field']] : $data, // region类型需要使用三个字段
                'options'     => isset($v['options']) ? $v['options'] : '',
                'require'     => isset($v['require']) ? true : false
            ]);
        // dump($data);die();
        }
        View::assign('form_html', $form_html);
        return view("{$this->tpl_dir}/dialog_form");
    }

    public function editPrev($data){
        return $data;
    }

    // public function saveDo()
    // {
    //     $data = request()->post();
    //     $data = $this->saveDoPrev($data);
    //     $data[$this->modules.'_addtime'] = time();
        
    //     $resp = Db::name($this->modules)->save($data);
    //     if($resp){
    //         return success('提交成功');
    //     } else {
    //         return error('提交失败');
    //     }
    // }

    public function saveDoPrev($data){
        return $data;
    }

    public function delDo(){
        $pk = request()->post('pk');

        Db::name($this->modules)->delete($pk);
        return success('删除成功');
    }
    // 密码加密盐
    public function password_salt($str)
    {
        $salt ='zx55wcv36bn';
        return md5($salt.$str);
    }
}