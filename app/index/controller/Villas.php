<?php
namespace app\index\controller;
use think\facade\Db;
use think\facade\View;

class Villas extends Base
{
    public function lists()
    {
        $model = Db::name('villas');
        if (input('get.villas_tag_id')) {
            $model->where('villas_tag', 'like', '%'.input('get.villas_tag_id').'%');
            $page_info = Db::name('villas_tag')->find(input('get.villas_tag_id'));
        } else {
            $page_info['villas_tag_name']   = 'All Villas';
            $page_info['villas_tag_detail'] = Db::name('config')->where('config_id', 7)->value('config_value');
            $page_info['villas_tag_banner'] = Db::name('villas_tag')->order('villas_tag_id ASC')->value('villas_tag_banner');
        }
        if (!empty(input('villas_name'))){
            $model->where('villas_name', 'like', '%'.input('villas_name').'%');
        }
        if (!empty(input('villas_bedrooms_num'))){
            $model->where('villas_bedrooms_num',input('villas_bedrooms_num'));
        }
        if (!empty(input('arrival_date')) && !empty(input('depart_date'))){
            $list = [];
            $date_from = input('arrival_date');
            $date_to   = input('depart_date');
            $date_from = strtotime($date_from);
            $date_to   = strtotime($date_to);
            for ($i=$date_from; $i<$date_to; $i+=86400) {
                $list[] = date("Y-m-d", $i);
            }
            $model1 = Db::name('reserve');
            $maps = [];
            foreach ($list as $v){
                $maps[] = [
                    ['reserve_create_time','<=',$v],
                    ['reserve_update_time','>',$v]
                ];
            }
            $arr = $model1->whereOr($maps)->column('reserve_hotel_id');
            $model->whereNotIn('villas_id',$arr);
        }
        // 收藏页面数据
        if (input('get.type') == 'shortlist') {
            $model->where('villas_id', 'in', cookie('shortlist'));
            $page_info['villas_tag_name']   = 'Shortlist';
        }
        
        $villas_list = $model->where('villas_status', 1)->order('villas_sort DESC')->select()->toArray();
        foreach ($villas_list as $k => $v) {
            $villas_list[$k] = self::_detail_format($v);
        }
        
        View::assign('page_info', $page_info);
        View::assign('villas_list', $villas_list);
        return view('/villas-lists');
    }

    public function detail()
    {
        $detail = Db::name('villas')->where('villas_id', input('get.villas_id'))->find();
        $detail = self::_detail_format($detail);
        $detail['quick_facts'] = Db::name('quick_facts')->where('villas_id', input('get.villas_id'))->order('quick_facts_sort DESC')->select()->toArray();
        $detail['reviews']     = Db::name('reviews')->where('villas_id', input('get.villas_id'))->order('reviews_sort DESC')->select()->toArray();
        $detail['villas_location'] = explode(',', $detail['villas_location']);
        $reserve = (new Promos)->get_reserve(input('get.villas_id'));
        View::assign('reserve', json_encode($reserve));
        View::assign('detail', $detail);
        return view('/villas-detail');
    }

    public static function _detail_format($item)
    {
        $item['villas_gallery']       = json_decode($item['villas_gallery'], true);
        $item['villas_banner']        = json_decode($item['villas_banner'], true);
        $item['villas_tag']           = $item['villas_tag'] ? Db::name('villas_tag')->where('villas_tag_id', 'in', $item['villas_tag'])->column('villas_tag_name') : [];
        $item['villas_hardware']      = explode(',', $item['villas_hardware']);
        $item['villas_selling_point'] = explode(',', $item['villas_selling_point']);
        return $item;
    }
}