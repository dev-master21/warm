<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class Category extends AdminBase
{
    public function _config(){
        $this->modules    = 'category';
        $this->pk         = 'category_id';
        $this->base_where = [];
        $this->order      = 'category_sort DESC';
        $this->top_btn    = "<button class=\"layui-btn layui-btn-sm\" onclick=\"dialog('/admin2022.php/".request()->controller()."/add.html', '添加')\">添加</button>";

        $this->search = [];

        $this->column = [
            ['field'=>'category_id','width'=>80,'title'=>'ID'],
            ['field'=>'category_name','title'=>'名称'],
            // ['field'=>'category_icon','title'=>'横幅'],
            // ['field'=>'category_is_single_str','title'=>'是否是单页'],
            // ['field'=>'category_info','title'=>'单页介绍'],
            ['field'=>'category_sort','title'=>'权重','width'=>120],
            ['field'=>'category_is_show_str','width'=>120,'title'=>'是否显示'],
            ['field'=>'sys_action','align'=>'left','width'=>120,'title'=>'操作']
        ];

        $category_pid_options = AdminQuick::select_options('category',[['category_pid','=',0]],'category_id','category_name');
        $category_pid_options = '0:= 一级分类 =;'.$category_pid_options;
        $this->add_form = [
            ['type'=>'select','title'=>'上级分类','field'=>'category_pid','options'=>$category_pid_options],
            ['type'=>'text','title'=>'名称','field'=>'category_name'],
            ['type'=>'image-multiple','title'=>'图集','field'=>'category_gallery'],
            ['type'=>'image','title'=>'横幅','field'=>'category_icon'],
            ['type'=>'text','title'=>'权重','field'=>'category_sort'],
            ['type'=>'editor','title'=>'简介','field'=>'category_detail'],
            // ['type'=>'radio','title'=>'是否单页','field'=>'category_is_single','options'=>'1:是;2:否'],
            // ['type'=>'image','title'=>'单页介绍','field'=>'category_info'],
        ];
        $this->tpl_dir = 'base';
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            $rows[$k]['sys_action']             = $this->_operate($v);
            $rows[$k]['category_icon']          = list_img_format($v['category_icon']);
            $rows[$k]['category_info']          = list_img_format($v['category_info']);
            $rows[$k]['category_is_single_str'] = $v['category_is_single'] == 1 ? '是' : '否';
            if ($rows[$k]['category_is_show'] == 1) {
                $rows[$k]['category_is_show_str'] = '<input type="checkbox" name="category_is_show" value="'.$v['category_id'].'" lay-skin="switch" checked lay-filter="switch_change">';
            } else {
                $rows[$k]['category_is_show_str'] = '<input type="checkbox" name="category_is_show" value="'.$v['category_id'].'" lay-skin="switch" lay-filter="switch_change">';
            }
        }
        return $rows;
    }

    public function _operate($row)
    {
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-warm layui-btn-xs\" onclick=\"dialog('/admin2022.php/Category/edit.html?category_id={$row['category_id']}', '编辑', ['80%', '800px'])\">编辑</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }
    
    // 删除一级分类时也要删除子分类
    public function delDo(){
        Db::name('category')->where('category_id', input('post.pk'))->delete();
        Db::name('category')->where('category_pid', input('post.pk'))->delete();
        return success('删除成功');
    }
    
    // 显示和隐藏切换
    public function switch_change()
    {
        $category_is_show = (input('post.status') == 'true') ? 1 : 0;
        Db::name('category')->where('category_id', input('post.pk'))->update(['category_is_show'=>$category_is_show]);
    }
}
?>