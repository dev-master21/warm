<?php
namespace app\index\controller;
use think\facade\Db;
use think\facade\View;

class Events extends Base
{
    public function index()
    {
        // $detail = Db::name('category')->where('category_id', input('get.category_id'))->find();
        // $detail['category_gallery'] = json_decode($detail['category_gallery'], true);
        // View::assign('detail', $detail);
        
        $villas_event_tag = Db::name('villas_tag')->where('villas_tag_type', 1)->select();
        View::assign('villas_event_tag', $villas_event_tag);
        
        return view('/events-index');
    }
}