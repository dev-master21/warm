<?php
namespace app\index\controller;
use think\facade\Db;
use think\facade\View;

class Contact extends Base
{
    public function index()
    {
        // $detail = Db::name('category')->where('category_id', input('get.category_id'))->find();
        // $detail['category_gallery'] = json_decode($detail['category_gallery'], true);
        // View::assign('detail', $detail);
        return view('/events-index');
    }
}