<?php
namespace app\index\controller;
use think\facade\Db;
use think\facade\View;

class Trips extends Base
{
    public function lists()
    {
        $trips_list = Db::name('trips')->order('trips_sort DESC')->select()->toArray();
        View::assign('trips_list', $trips_list);
        return view('/trips-lists'); // 渲染对应的视图
    }

    public function detail()
    {
        $trips_id = input('trips_id');
        $trips_info = Db::name('trips')->where('trips_id', $trips_id)->find();
        View::assign('trips_info', $trips_info);
        return view('/trips-detail'); // 渲染对应的视图
    }
}