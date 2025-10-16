<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\View;
use think\facade\Cookie;

class Villas extends AdminBase
{
    public function _config(){
        $this->modules    = 'villas';
        $this->pk         = 'villas_id';
        $this->base_where = [];
        $this->order      = 'villas_id DESC,villas_sort DESC';
        $this->top_btn = "<button class=\"layui-btn layui-btn-sm\" onclick=\"dialog('/admin2022.php/".request()->controller()."/add.html', '添加')\">添加</button>";

        $this->search = [
            "villas_title" => 'input|标题',
        ];

        $this->column = [
            ['field'=>'villas_id','width'=>90,'title'=>'ID'],
            // ['title'=>'城市','field'=>'villas_city'],
            ['title'=>'名称','field'=>'villas_name','width'=>200],
            ['title'=>'ICAL','field'=>'villas_ical'],
            ['title'=>'原价','field'=>'villas_original_price','width'=>100],
            ['title'=>'现价','field'=>'villas_price','width'=>100],
            ['title'=>'现价','field'=>'villas_price','width'=>100],
            ['title'=>'价格计划','field'=>'villas_price_plan_html','width'=>100],
            ['title'=>'价格图','field'=>'villas_price_image','width'=>100],
            ['title'=>'布局图','field'=>'villas_blueprint','width'=>100],
            ['title'=>'卧室数量','field'=>'villas_bedrooms_num','width'=>100],
            ['title'=>'成人数量','field'=>'villas_adults_num','width'=>100],
            ['title'=>'儿童数量','field'=>'villas_children_num','width'=>100],
            ['title'=>'权重','field'=>'villas_sort','width'=>100],
            ['field'=>'villas_status_temp','title'=>'是否显示','width'=>100],
            ['field'=>'reviews_html','title'=>'Reviews','width'=>100],
            ['field'=>'quick_facts_html','title'=>'Quick Facts','width'=>120],
            ['field'=>'sys_action','align'=>'left','width'=>150,'fixed'=>'right','title'=>'操作']
        ];
        
        $this->add_form = [
            ['type'=>'text','title'=>'城市','field'=>'villas_city'],
            ['type'=>'text','title'=>'名称','field'=>'villas_name'],
            ['type'=>'text','title'=>'ICAL','field'=>'villas_ical'],
            ['type'=>'textarea','title'=>'简述','field'=>'villas_desc'],
            ['type'=>'image','title'=>'价格图','field'=>'villas_price_image'],
            ['type'=>'image','title'=>'封面','field'=>'villas_cover'],
            ['type'=>'image-multiple','title'=>'顶部图集','field'=>'villas_banner'],
            ['type'=>'image-multiple','title'=>'详情图集','field'=>'villas_gallery'],
            ['type'=>'text','title'=>'720云链接','field'=>'villas_720url'],
            ['type'=>'text','title'=>'权重','field'=>'villas_sort'],
            ['type'=>'text','title'=>'详情标题','field'=>'villas_detail_banner_title'],
            ['type'=>'textarea','title'=>'详情简述','field'=>'villas_detail_banner_desc'],
            ['type'=>'text','title'=>'经纬度','field'=>'villas_location'],
            ['type'=>'image','title'=>'别墅局图','field'=>'villas_blueprint'],
            ['type'=>'text','title'=>'卧室数量','field'=>'villas_bedrooms_num'],
            ['type'=>'text','title'=>'成人数量','field'=>'villas_adults_num'],
            ['type'=>'text','title'=>'儿童数量','field'=>'villas_children_num'],
            ['type'=>'checkbox','title'=>'标签','field'=>'villas_tag','options'=>AdminQuick::select_options('villas_tag', [], 'villas_tag_id', 'villas_tag_name')],
            ['type'=>'checkbox','title'=>'硬件设施','field'=>'villas_hardware','options'=>'Staffed:Staffed;Private Chef:Private Chef;Private Pool:Private Pool;Elite Concierge:Elite Concierge;Spa Services:Spa Services;Free Wifi:Free Wifi'],
            ['type'=>'checkbox','title'=>'别墅特点','field'=>'villas_selling_point','options'=>'Rice Field or River View:Rice Field or River View;Gym Facilities:Gym Facilities;Tennis Court:Tennis Court;Cinema:Cinema;Media/Games:Media/Games;RoomBunk Room:RoomBunk Room;Culinary delights:Culinary delights'],
        ];
        $this->tpl_dir = 'base';
    }

    public function saveDoPrev($data)
    {
        $data['villas_selling_point'] = isset($data['villas_selling_point']) ? implode(',', $data['villas_selling_point']) : '';
        $data['villas_hardware']      = isset($data['villas_hardware']) ? implode(',', $data['villas_hardware']) : '';
        $data['villas_tag']           = isset($data['villas_tag']) ? implode(',', $data['villas_tag']) : '';
        return $data;
    }

    public function listsFormat($rows)
    {
        foreach ($rows as $k => $v) {
            $rows[$k]['villas_cover']           = list_img_format($v['villas_cover']);
            $rows[$k]['villas_blueprint']       = list_img_format($v['villas_blueprint']);
            $rows[$k]['villas_price_image']     = list_img_format($v['villas_price_image']);
            $rows[$k]['sys_action']             = $this->_operate($v);
            $rows[$k]['quick_facts_html']       = "<a class=\"layui-btn layui-btn-normal layui-btn-xs\" onclick=\"dialog('/admin2022.php/QuickFacts/lists.html?wheremap__villas_id={$v['villas_id']}', 'Quick Facts')\">Quick Facts</a>";
            $rows[$k]['reviews_html']           = "<a class=\"layui-btn layui-btn-normal layui-btn-xs\" onclick=\"dialog('/admin2022.php/Reviews/lists.html?wheremap__villas_id={$v['villas_id']}', 'Reviews')\">Reviews</a>";
            $rows[$k]['villas_price_plan_html'] = "<a class=\"layui-btn layui-btn-normal layui-btn-xs\" onclick=\"dialog('/admin2022.php/VillasPricePlan/lists.html?wheremap__villas_id={$v['villas_id']}', 'Price Plan')\">Price Plan</a>";
            if ($rows[$k]['villas_status'] == 1) {
                $rows[$k]['villas_status_temp'] = '<input type="checkbox" name="villas_status" value="'.$v['villas_id'].'" lay-skin="switch" checked lay-filter="switch_change">';
            } else {
                $rows[$k]['villas_status_temp'] = '<input type="checkbox" name="villas_status" value="'.$v['villas_id'].'" lay-skin="switch" lay-filter="switch_change">';
            }
        }
        return $rows;
    }

    public function _operate($row)
    {
        $html  = '';
        $html .= "<a class=\"layui-btn layui-btn-normal layui-btn-xs\" onclick=\"dialog('/admin2022.php/".request()->controller()."/edit.html?villas_id={$row['villas_id']}', '编辑')\">编辑</a>";
        $html .= "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>";
        return $html;
    }

    // public function editPrev($data)
    // {
    //     dump($data);die;
    // }

    public function switch_change()
    {
        $villas_status = (input('post.status') == 'true') ? 1 : 0;
        Db::name('villas')->where('villas_id', input('post.pk'))->update(['villas_status'=>$villas_status]);
    }
}
?>