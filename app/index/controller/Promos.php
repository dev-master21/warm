<?php
namespace app\index\controller;
use think\facade\Db;
use think\facade\View;

class Promos extends Base
{
    public function lists()
    {
        $villas_list = Db::name('villas')->where('villas_status', 1)->where('`villas_original_price` > `villas_price`')->where('villas_original_price', '>', 0)->where('villas_price', '>', 0)->order('villas_sort DESC')->select()->toArray();
        
        foreach ($villas_list as $k => $v) {
            $villas_list[$k] = Villas::_detail_format($v);
            $villas_list[$k]['rebate'] = round(($v['villas_original_price'] - $v['villas_price']) / $v['villas_original_price'],2) * 100;
        }
        View::assign('villas_list', $villas_list);
        return view('/promos-lists');
    }

    public function detail()
    {
        // $detail = Db::name('category')->where('category_id', input('get.category_id'))->find();
        // $detail['category_gallery'] = json_decode($detail['category_gallery'], true);
        // View::assign('detail', $detail);
        return view('/promos-detail');
    }

    // 拉取数据
    public function get_content()
    {
        $data = Db::name('villas')->where('villas_ical','<>','')->order('villas_update_time ASC')->limit(1)->find();
        Db::name('villas')->where('villas_id',$data['villas_id'])->update(['villas_update_time'=>time()]);
        
        if ($data['villas_ical']){
            $list = [];
            $file_get_contents = file_get_contents($data['villas_ical']);
            // echo $file_get_contents;
            $file_get_contents = str_replace(array("--", "<br/>", "\t", "\r\n", "\r", "\n"), "[nnn]", $file_get_contents);
            preg_match_all('/BEGIN:VEVENT(.*)END:VEVENT/U',$file_get_contents,$result);
            foreach ($result[1] as $v){
                $time = $this->get_ics_dttime($v,'DTSTART','DTEND');
                $list[] = [
                    'reserve_hotel_id'=>$data['villas_id'],
                    'reserve_create_time'=>date('Y-m-d',strtotime($time['dt_start'])),
                    'reserve_update_time'=>date('Y-m-d',strtotime($time['dt_end']))
                ];
            }
            Db::name('reserve')->where('reserve_hotel_id', $data['villas_id'])->delete();
            Db::name('reserve')->insertAll($list);
            return json(['success'=>'success ID'.$data['villas_id']]);
        }
    }
    
    public function get_content_copy()
    {
        $data = Db::name('villas')->where('villas_ical','<>','')->order('villas_update_time ASC')->limit(1)->find();
        Db::name('villas')->where('villas_id',$data['villas_id'])->update(['villas_update_time'=>time()]);
        
        if ($data['villas_ical']){
            $list = [];
            $file_get_contents = file_get_contents($data['villas_ical']);
            // echo $file_get_contents;
            $file_get_contents = str_replace(array("--", "<br/>", "\t", "\r\n", "\r", "\n"), "[nnn]", $file_get_contents);
            preg_match_all('/BEGIN:VEVENT(.*)END:VEVENT/U',$file_get_contents,$result);
            foreach ($result[1] as $v){
                $time = $this->get_ics_dttime($v,'DTSTART','DTEND');
                if (strtotime($time['dt_end']) > time()){
                    $list[] = [
                        'reserve_hotel_id'=>$data['villas_id'],
                        'reserve_create_time'=>date('Y-m-d',strtotime($time['dt_start'])),
                        'reserve_update_time'=>date('Y-m-d',strtotime($time['dt_end']))
                    ];
                }
            }
            Db::name('reserve')->where('reserve_hotel_id', $data['villas_id'])->where('reserve_update_time','>',date('Y-m-d',time()))->delete();
            Db::name('reserve')->insertAll($list);
            return json(['success'=>'success ID'.$data['villas_id']]);
        }
    }

    // 获取预定数据
    public function get_reserve($villas_id)
    {
        $datas = Db::name('reserve')->where('reserve_hotel_id', $villas_id)->select()->toArray();
        $list = [];
        foreach ($datas as $data){
            $date_from = $data['reserve_create_time'];
            $date_to = $data['reserve_update_time'];

            $date_from = strtotime($date_from);

            $date_to = strtotime($date_to);
            for ($i=$date_from; $i<$date_to; $i+=86400)
            {
                
                
                
                
                $list[date("Y-n-j", $i)] = 'Sold';
            }
        }
        return $list;
    }
    
    public function get_ics_dttime($str, $stag, $etag)
    {
        $time = [];
        $cache = explode('[nnn]', $str);
        foreach ($cache as $vv) {
            if (strpos($vv, $stag) !== false) {
                $c = explode(':', $vv);
                $time['dt_start'] = $c[1];
            }
            if (strpos($vv, $etag) !== false) {
                $c = explode(':', $vv);
                $time['dt_end'] = $c[1];
            }
        }
        return $time;
    }
}